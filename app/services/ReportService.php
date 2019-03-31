<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ReportService extends Injectable
{
    protected $report;
    protected $year;
    protected $month;
    protected $dayStart; // will use this
    protected $dayEnd;   // will use this

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
#           'wsong365@gmail.com',
        ];

        foreach ($users as $user) {
            $this->sendEmail($user, $subject, $body, $filename);
        }

        $this->log("Report sent completed.\n");
    }

    public function getErthmeterReport($year, $month)
    {
        $this->year = $year;
        $this->month = $month;

        $tm = mktime(0, 0, 0, $month, 1, $year);

        $dayStart = 1;
        $dayEnd = date('t', $tm); // days of the month
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

                $erthmeter = $project->getErthmeter($date);
                if (!$erthmeter) {
                    #echo "SELECT * FROM erthmeter WHERE recorder_id='$erthid' AND date='$date'", EOL;
                    continue;
                }

                for ($hour = 0; $hour < 24; $hour++) {
                    $start = sprintf('%s %02d:00:00', $date, $hour);
                    $end   = sprintf('%s %02d:59:59', $date, $hour);

                    $power = $project->getTotalPower($start, $end);

                    $key = 'T'.($hour+1); // T1,T2,T3...T24
                    $rate = $erthmeter[$key];

                    $project->totalPower += $power;
                    $project->totalAmount += $power*$rate/1000.0;

                    if ($power + $rate > 0) {
                        #echo "$id) $start $end  $power x $rate = ", $power*$rate, EOL;
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
        $sheet->setCellValue("E4", date('F-d-Y'));

        $row = 8;
        foreach ($report as $project) {
            $sheet->setCellValue("C$row", $project->storeNumber);
            $sheet->setCellValue("D$row", $project->siteName);
            $sheet->setCellValue("E$row", $project->totalPower);
            $sheet->setCellValue("F$row", $project->totalAmount);
            $row++;
        }

        $suffix = date('Ymd');
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
