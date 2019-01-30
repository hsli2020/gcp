<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ReportService extends Injectable
{
    public function getErthmeterReport($year, $month)
    {
        $dayStart = 1;
        $dayEnd = 15;

        $projects = $this->projectService->getAll();

        foreach ($projects as $project) {
            $id = $project->id;
            $name = $project->name;
            $erthid = $project->erthmeterId;

            if (strlen($project->erthmeterId) == 0) {
                continue;
            }

            echo "$id) $name ($erthid)", EOL;

            for ($day = $dayStart; $day <= $dayEnd; $day++) {
                $date = sprintf('%d-%02d-%02d', $year, $month, $day);

                $erthmeter = $project->getErthmeter($date);
                if (!$erthmeter) {
                    echo "SELECT * FROM erthmeter WHERE recorder_id='$erthid' AND date='$date'", EOL;
                    continue;
                }

                for ($hour = 0; $hour < 24; $hour++) {
                    $start = sprintf('%s %02d:00:00', $date, $hour);
                    $end   = sprintf('%s %02d:59:59', $date, $hour);

                    $power = $project->getTotalPower($start, $end);

                    $key = 'T'.($hour+1); // T1,T2,T3...T24
                    $rate = $erthmeter[$key];

                    if ($power + $rate > 0) {
                        echo "$id) $start $end  $power x $rate = ", $power*$rate, EOL;
                    }
                }
                echo EOL;
            }
            echo EOL;
        }
    }

    public function sendErthmeterReport()
    {
        // $this->getErthmeterReport();
        // makeExcel
        // sendMail
    }
}
