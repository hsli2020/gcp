<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ReportService extends Injectable
{
    protected $report;
    protected $year;
    protected $month;
    protected $dayStart;
    protected $dayEnd;

    public function send()
    {
        if (!$this->report) {
            // call getErthmeterReport first to generate report
            // $this->getErthmeterReport($year, $month);
            return;
        }

        $this->log('Start sending erthmeter report');

        $report = $this->report;
        $filename = $this->generateXls($report);
        $body = $this->generateHtml($report);

        $today = date('Y-m-d');
        $subject = "GCP Erthmeter Report ($today)";

        $users = [
            'lihsca@gmail.com',
            'wsong365@gmail.com',
            'dmacabales@greatcirclesolar.ca',
        ];

        foreach ($users as $user) {
            $this->sendEmail($user, $subject, $body, $filename);
        }

        $this->log("Report sent completed.\n");
    }

    public function getErthmeterReport($year, $month, $dayStart = 1, $dayEnd = 0)
    {
        $this->downloadPriceReport();
        $this->importPriceReport();

        $this->year = $year;
        $this->month = $month;
        $this->dayStart = $dayStart;
        $this->dayEnd = $dayEnd;

        if ($dayEnd <= 0) {
            $tm = mktime(0, 0, 0, $month, 1, $year);
            $this->dayEnd = $dayEnd = date('t', $tm); // days of the month
        }

        $report = [];

        $projects = $this->projectService->getAll();

        foreach ($projects as $project) {
            $id = $project->id;
            $name = $project->name;
            $erthid = $project->erthmeterId;

            $project->totalPower = 0;
            $project->totalAmount = 0;

            if (strlen($project->erthmeterId) == 0) {
                continue;
            }

            echo "$id) $name ($erthid)", EOL;

            for ($day = $dayStart; $day <= $dayEnd; $day++) {
                $date = sprintf('%d-%02d-%02d', $year, $month, $day);

                $prices = $this->getPrices($date);
                if (!$prices) {
                    echo "NO PRICES: $date", EOL;
                    continue;
                }

                $powers = $this->getPowers($erthid, $date);

                for ($hour = 1; $hour <= 24; $hour++) {
                    $price = $prices[$hour];
                    $power = $powers["T$hour"];

                    $project->totalPower += $power;
                    $project->totalAmount += $power*$price/1000.0;

                    if ($power + $price > 0) {
                        #echo "$id) $date $power x $price = ", $power*$price, EOL;
                    }
                }
            }

            $project->totalAmount = round($project->totalAmount ,2);
            $report[$id] = $project;
        }

        $this->report = $report;

        return $report;
    }

    public function generateXls($report)
    {
        $excel = \PHPExcel_IOFactory::load(BASE_DIR."/job/templates/Erthmeter.xlsx");
        $excel->setActiveSheetIndex(0);  //set first sheet as active

        $sheet = $excel->getActiveSheet();
        $sheet->setCellValue("E4", $this->year.'-'.$this->month);

        $row = 8;
        foreach ($report as $project) {
            $sheet->setCellValue("C$row", $project->storeNumber);
            $sheet->setCellValue("D$row", $project->siteName);
            $sheet->setCellValue("E$row", $project->totalPower);
            $sheet->setCellValue("F$row", $project->totalAmount);
            $row++;
        }

        $suffix = $this->year.'-'.$this->month;
        $filename = BASE_DIR . "/app/logs/Erthmeter-$suffix.xlsx";

        $xlsWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $xlsWriter->save($filename);

        return $filename;
    }

    public function generateHtml($report)
    {
        ob_start();
        $date = date('F d, Y');
        include(BASE_DIR . "/job/templates/Erthmeter.tpl");
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function downloadPriceReport()
    {
        $filename = BASE_DIR. '/tmp/hoep-price.csv';

        if (file_exists($filename) && (time() - filemtime($filename) < 12*3600)) {
            return;
        }

        $url = "http://reports.ieso.ca/public/PriceHOEPPredispOR/PUB_PriceHOEPPredispOR.csv";
        $content = file_get_contents($url);
        file_put_contents($filename, $content);
    }

    public function importPriceReport()
    {
        if (!($fp = fopen(BASE_DIR. '/tmp/hoep-price.csv', 'r'))) {
            return;
        }

        $this->db->execute('TRUNCATE TABLE hoep_price');

        // Skip first three lines
        fgets($fp);
        fgets($fp);
        fgets($fp);

        $columns = fgetcsv($fp);

        while (($values = fgetcsv($fp)) !== false) {
            if (count($columns) != count($values)) {
                continue;
            }
            $fields = array_combine($columns, $values);
            $this->db->insertAsDict('hoep_price', [
                'date'               => $fields['Date'],
                'hour'               => $fields['Hour'],
                'hoep'               => $fields['HOEP'],
                'hour_1_predispatch' => $fields['Hour 1 Predispatch'],
                'hour_2_predispatch' => $fields['Hour 2 Predispatch'],
                'hour_3_predispatch' => $fields['Hour 3 Predispatch'],
                'or_10_min_sync'     => $fields['OR 10 Min Sync'],
                'or_10_min_non_sync' => $fields['OR 10 Min non-sync'],
                'or_30_min'          => $fields['OR 30 Min'],
            ]);
        }

        fclose($fp);
    }

    public function getPrices($date)
    {
        $sql = "SELECT hour, hoep FROM hoep_price WHERE date='$date'";
        $rows = $this->db->fetchAll($sql);
        return array_column($rows, 'hoep', 'hour');
    }

    public function getPowers($erthid, $date)
    {
        $sql = "SELECT * FROM erthmeter WHERE recorder_id='$erthid' AND date='$date' AND ch='03'";
        $row = $this->db->fetchOne($sql);
        return $row;
    }

    public function sendEmail($recepient, $subject, $body, $filename = '')
    {
        $mail = new \PHPMailer();

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $today = date('Y-m-d');

#       $mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->Host = '10.6.200.200';
        $mail->Port = 25;
        $mail->SMTPAuth = false;
        $mail->SMTPSecure = false;
        $mail->From = "OMS@greatcirclesolar.ca";
        $mail->FromName = "Great Circle Solar";
        $mail->addAddress($recepient);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = "Please find the Report in attachment.";

        if ($filename) {
            $mail->addAttachment($filename, basename($filename));
        }

        if (!$mail->send()) {
            $this->log("Mailer Error: " . $mail->ErrorInfo);
        } else {
            $this->log("Report sent to $recepient.");
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
