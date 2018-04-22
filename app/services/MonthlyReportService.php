<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class MonthlyReportService extends Injectable
{
    protected $report;

    public function generate()
    {
        echo "Generating Monthly Report ...", EOL;

        $projects = $this->projectService->getAll();

        $this->report = [];
        foreach ($projects as $project) {
            $projectId = $project->id;

            $Project_Name = $project->name;
            $Date         = date('M-Y');

            $this->report[$projectId] = [
                'Project_Name'          =>  $Project_Name,
                'Date'                  =>  $Date,
            ];
        }

#       unset($this->report[7]); // remove Norfolk from MonthlyReport

        $this->save();

        return $this->report;
    }

    public function save()
    {
        $filename = $this->getFilename(date('Ymd'));
        $json = json_encode($this->report, JSON_PRETTY_PRINT);
        file_put_contents($filename, $json);

        try {
            $this->db->insertAsDict('monthly_reports', [
                'month'  => date('Y-m'),
                'report' => $json,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function load($month, $user = null)
    {
        $sql = "SELECT * FROM monthly_reports WHERE month='$month'";
        $result = $this->db->fetchOne($sql);
        if ($result) {
            $report = json_decode($result['report'], true);
            $report = $this->getUserSpecificReports($user, $report);
            return $report;
        }
        return [];
    }

    public function send()
    {
        echo "Sending Monthly Report ...", EOL;

        $this->log('Start sending monthly report');

        $users = $this->userService->getAll();

        foreach ($users as $user) {
            if ($user['monthlyReport'] == 0) {
                continue;
            }

            if (strpos($user['email'], '@') === false) {
                $this->log("Skip sending monthly report to {$user['username']}, no email.");
                continue;
            }

            $lastMonth = date('Y-m', strtotime('-1 month'));
            $report = $this->load($lastMonth, $user);

            $filename = $this->generateXls($report);
            $html = $this->generateHtml($report);

            $this->sendMonthlyReport($user['email'], $html, $filename);
        }

        $this->log("Monthly report sending completed.\n");
    }

    protected function getFilename($date)
    {
        return BASE_DIR . "/app/logs/monthly-report-$date.json";
    }

    public function generateXls($report, $month = null)
    {
        $excel = \PHPExcel_IOFactory::load(BASE_DIR."/job/templates/MonthlyReport-v1.xlsx");
        $excel->setActiveSheetIndex(0);  //set first sheet as active

        $monthYear = $month ? $month : date('F Y');
        $sheet = $excel->getActiveSheet();
        $sheet->setCellValue("B1", "MONTHLY REPORT SUMMARY\n$monthYear");

        $row = 5;

        foreach ($report as $data) {
            $sheet->setCellValue("B$row", $data['Project_Name']);
            $sheet->setCellValue("C$row", $data['Date']);
            $row++;
        }

        $suffix = $month ? $month : date('Y-m');
        $filename = BASE_DIR . "/app/logs/MonthlyReport-$suffix.xlsx";

        $xlsWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $xlsWriter->save($filename);

        return $filename;
    }

    protected function generateHtml($report)
    {
        ob_start();
        $date = date('F, Y', strtotime('-1 month'));
        include("./templates/monthly-report.tpl");
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function getUserSpecificReports($user, $report)
    {
        if (!$user) {
            return $report;
        }

        $result = [];

        $projects = $this->userService->getUserProjects($user['id']);

        foreach ($projects as $id) {
            if (isset($report[$id])) {
                $result[$id] = $report[$id];
            }
        }

        return $result;
    }

    protected function sendMonthlyReport($recepient, $body, $filename)
    {
        $today = date('Y-m-d');
        $subject = "Monthly Solar Energy Production Report ($today)";

        if (!$this->emailService->send($recepient, $subject, $body, $filename)) {
            $this->log("Mailer Error: " . $this->emailService->getErrorInfo());
        } else {
            $this->log("Monthly report sent to $recepient.");
        }
    }

    protected function log($str)
    {
        $filename = BASE_DIR . '/app/logs/report.log';

        if (file_exists($filename) && filesize($filename) > 128*1024) {
            unlink($filename);
        }

        $str = date('Y-m-d H:i:s ') . $str . "\n";

        echo $str;
        error_log($str, 3, $filename);
    }
}
