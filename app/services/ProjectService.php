<?php

namespace App\Service;

use Phalcon\Di\Injectable;

use App\System\Project;

class ProjectService extends Injectable
{
    protected $projects = [];

    public function getAll(/* $includeInactive = false */)
    {
        if (!$this->projects) {
            $sql = "SELECT * FROM project WHERE active=1";
            $projects = $this->db->fetchAll($sql);

            foreach ($projects as $project) {
                $id = $project['id'];
                $object = new Project($project);

                $sql = "SELECT * FROM device WHERE project_id='$id'";
                $devices = $this->db->fetchAll($sql);

                foreach ($devices as $device) {
                    $object->initDevices($device);
                }

                $this->projects[$id] = $object;
            }
        }

#       unset($this->projects[7]); // remove Norfolk, it affects everywhere

        return $this->projects;
    }

    public function get($id)
    {
        $this->getAll();

        if (isset($this->projects[$id])) {
            return $this->projects[$id];
        }

        throw new \Exception("Invalid Parameter: $id");
    }

    public function getDetails($id)
    {
        $info = [];

        $project = $this->get($id);
        $latest = $project->getLatest();
        $snapshot = $project->getSnapshot();
        $alarms = $project->getAlarms();

        $info['project'] = $project;
        $info['details'] = $latest + $snapshot;
        $info['alarms'] = $alarms;

        return $info;
    }

    public function getErthmeterReport($year, $month)
    {
        $dayStart = 1;
        $dayEnd = 15;

        $projects = $this->getAll();

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
