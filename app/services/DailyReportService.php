<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class DailyReportService extends Injectable
{
    protected $report;

    public function generate()
    {
        echo "Generating Daily Report ...", EOL;

        $projects = $this->projectService->getAll();

        $this->report = [];
        foreach ($projects as $project) {
            $projectId = $project->id;

            $Project_Name        = $project->name;
            $Date                = date('d/m/Y');
            $Capacity_AC         = $project->capacityAC;
            $Capacity_DC         = $project->capacityDC;;

            $this->report[$projectId] = [
                'Project_Name'          =>  $Project_Name,
                'Date'                  =>  $Date,
                'Capacity_AC'           =>  number_format($Capacity_AC,         1, '.', ''),
                'Capacity_DC'           =>  number_format($Capacity_DC,         1, '.', ''),
            ];
        }

#       unset($this->report[7]); // remove Norfolk from DailyReport

        $this->save();

        return $this->report;
    }

    public function save()
    {
        $json = json_encode($this->report, JSON_PRETTY_PRINT);

        if (0) {
            $filename = $this->getFilename(date('Ymd'));
            file_put_contents($filename, $json);
        }

        try {
            $this->db->insertAsDict('daily_reports', [
                'date'   => date('Y-m-d'),
                'report' => $json,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function load($date, $user = null)
    {
        $sql = "SELECT * FROM daily_reports WHERE date='$date'";
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
        echo "Sending Daily Report ...", EOL;

        $this->log('Start sending daily report');

        $users = $this->userService->getAll();

        foreach ($users as $user) {
            if ($user['dailyReport'] == 0) {
                continue;
            }

            if (strpos($user['email'], '@') === false) {
                $this->log("Skip sending daily report to {$user['username']}, no email.");
                continue;
            }

            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $report = $this->load($yesterday, $user);

            $filename = $this->generateXls($report);
            $html = $this->generateHtml($report);

            $this->sendDailyReport($user['email'], $html, $filename);
        }

        $this->log("Daily report sending completed.\n");
    }

    protected function getFilename($date)
    {
        return BASE_DIR . "/app/logs/daily-report-$date.json";
    }

    public function generateXls($report, $date = null)
    {
        $excel = \PHPExcel_IOFactory::load(BASE_DIR."/job/templates/DailyReport-v3.xlsx");
        $excel->setActiveSheetIndex(0);  //set first sheet as active

        $sheet = $excel->getActiveSheet();
        $sheet->setCellValue("B3", date('F-d-Y', strtotime($date)));

        $row = 10;
        $index = 1;

        foreach ($report as $data) {
            $sheet->setCellValue("A$row", $index++);
            $sheet->setCellValue("B$row", $data['Project_Name']);
            $sheet->setCellValue("C$row", $data['Date']);
            $sheet->setCellValue("D$row", $data['Capacity_AC']);
            $sheet->setCellValue("E$row", $data['Capacity_DC']);
            $row++;
        }

        $suffix = $date ? $date : date('Ymd');
        $filename = BASE_DIR . "/app/logs/DailyReport-$suffix.xlsx";

        $xlsWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $xlsWriter->save($filename);

        return $filename;
    }

    protected function generateHtml($report)
    {
        ob_start();
        $date = date('F d, Y', strtotime('yesterday'));
        include("./templates/daily-report.tpl");
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

    protected function sendDailyReport($recepient, $body, $filename)
    {
        $today = date('Y-m-d');
        $subject = "Daily Solar Energy Production Report ($today)";

        if (!$this->emailService->send($recepient, $subject, $body, $filename)) {
            $this->log("Mailer Error: " . $this->emailService->getErrorInfo());
        } else {
            $this->log("Daily report sent to $recepient.");
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
