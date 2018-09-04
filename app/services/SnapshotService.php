<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class SnapshotService extends Injectable
{
    public function load()
    {
        $sql = "SELECT *, CONVERT_TZ(time_utc, 'UTC', 'EST') AS time FROM snapshot";
        $result = $this->db->fetchAll($sql);

       #$auth = $this->session->get('auth');
       #if (!is_array($auth)) {
       #    return []; // if user not logged in, display nothing
       #}

       #$userProjects = $this->userService->getUserProjects($auth['id']);

        $rows = [];
        $power = 0;
        $generators = 0;

        foreach ($result as $key => $val) {
           #if (!in_array($val['project_id'], $userProjects)) {
           #    continue; // current user doesn't have permission to the project
           #}

            $rows[$key] = $result[$key];
            if ($val['M_Gen_real_enrg'] > 0) {
                $generators += 1;
            }

            $power += $val['M_Gen_real_enrg'];
        }

        $sql = "INSERT INTO generator_power (generators, power) VALUE ($generators, $power)";
        $this->db->execute($sql);

        $data = [];
        $data['rows'] = $rows;
        $data['project_count'] = count($rows);
        $data['power'] = round($power/1000.0, 1);
        $data['generators'] = $generators;

        $data['peak_hour'] = '00';
        $data['peak_energy'] = 0;

        $sql = "SELECT * FROM forecast_peak ORDER BY time_utc DESC LIMIT 1";
        $result = $this->db->fetchOne($sql);
        if ($result) {
            $data['peak_hour'] = $result['peak_hour'];
            $data['peak_energy'] = number_format($result['peak_energy']);
        }

        return $data;
    }

    public function generate()
    {
        echo 'Snapshot generating...';

        $projects = $this->projectService->getAll();

        foreach ($projects as $project) {
            $id = $project->id;

            $row = $this->db->fetchOne("SELECT * FROM latest WHERE project_id=$id");
            if (!$row) {
                continue;
            }

            // NOTICE: table `snapshot` contains all fields from table `latest`
            $data = json_decode($row['data'], true);

            if ($project->operationMode == 'Closed Transition') {
                $data['EZ_G_13'] = 9; // N/A
            }

            $fields = '';
            foreach ($data as $key => $val) {
                $fields .= ",$key = '$val'";
            }

            $alarm = $this->getAlarm($data, $project);
            $ureaLevel = $this->getUreaLevel($project);

            $sql = "REPLACE INTO snapshot SET"
                . ' project_id='.       $row['project_id']
                . ",project_name='".    $row['project_name']."'"
                . ",devcode='".         $row['devcode']."'"
                . ",devtype='".         $row['devtype']."'"
#               . ",time_utc='".        $row['time']."'"
                . ",data='".            $row['data']."'"
                . $fields               // NOTICE: table `snapshot` contains all fields from table `latest`
                . ',project_alarm='.    $alarm
                . ',urea_level='.       $ureaLevel;

            $this->db->execute($sql);
        }
    }

    protected function getAlarm($data, $project)
    {
        static $tags = [];

        if (empty($tags)) {
            $rows = $this->db->fetchAll("SELECT * FROM modbus WHERE address>=20613 AND address<=20644");
            $tags = array_column($rows, 'tag_name', 'address');
        }

       #if ($project->operationMode == 'Closed Transition') {
       #    return 9; // N/A
       #}

        $alarm = 0;
        foreach ($tags as $key => $tag) {
            if (isset($data[$tag]) && $data[$tag] != 0) {
                $alarm = 1;
                break;
            }
        }

        return $alarm;
    }

    protected function getUreaLevel($project)
    {
        $tagName = "ML060_o";

        $id = $project->id;

        $sql = "SELECT * FROM safety_power WHERE project_id=$id";
        $row = $this->db->fetchOne($sql);

        if ($row && $row['data']) {
            $json = json_decode($row['data']);
            if ($json) {
                foreach ($json->payload as $payload) {
                    if ($payload->name == $tagName) {
                        return $payload->laststate->value;
                    }
                }
            }
        }

        return 0;
    }

    public function getChartData()
    {
        $today = date('Y-m-d');

        $sql = "SELECT * FROM generator_power WHERE DATE(time)='$today' GROUP BY UNIX_TIMESTAMP(time) DIV 300";
        $rows = $this->db->fetchAll($sql);

        // utc time to local time
        $gens  = [];
        $power = [];
        foreach ($rows as $row) {
            $time = strtotime($row['time'].' UTC');
            $time -= $time%60; // floor to minute (no seconds)
            $gens[$time] = [ $time*1000, intval($row['generators']) ];
            $power[$time] = [ $time*1000, intval($row['power']) ];
        };

        return [ 'gens' => array_values($gens), 'power' => array_values($power) ];
    }
}
