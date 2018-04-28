<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class SnapshotService extends Injectable
{
    public function load()
    {
        $result = $this->db->fetchAll("SELECT * FROM snapshot");

       #$auth = $this->session->get('auth');
       #if (!is_array($auth)) {
       #    return []; // if user not logged in, display nothing
       #}

       #$userProjects = $this->userService->getUserProjects($auth['id']);

        $data = [];
        foreach ($result as $key => $val) {
           #if (!in_array($val['project_id'], $userProjects)) {
           #    continue; // current user doesn't have permission to the project
           #}

            $data[$key] = $result[$key];
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

            $data = json_decode($row['data'], true);

            $alarm = $this->getAlarm($data, $project);
            $ureaLevel = $this->getUreaLevel($project);

            $sql = "REPLACE INTO snapshot SET"
                . ' project_id='.       $row['project_id']
                . ",project_name='".    $row['project_name']."'"
                . ',Genset_Status='.    $data['Genset_Status']
                . ',Emergency_Mode='.   $data['Emergency_Mode']
                . ',M_Start_Auto='.     $data['M_Start_Auto']
                . ',Total_Gen_Power='.  $data['Total_Gen_Power']
                . ',Total_mains_pow='.  $data['Total_mains_pow']
                . ',Dig_Input_1='.      $data['Dig_Input_1']
                . ',Dig_Input_0='.      $data['Dig_Input_0']
                . ',EZ_G_13='.          $data['EZ_G_13']
                . ',M_Start_Inhibit='.  $data['M_Start_Inhibit']
                . ',RTAC_Perm_Stat='.   $data['RTAC_Perm_Stat']
                . ',RTAC_Allow='.       $data['RTAC_Allow']
                . ',RTAC_Trip='.        $data['RTAC_Trip']
                . ',RTAC_Block='.       $data['RTAC_Block']
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

        if ($project->operationMode == 'Closed Transition') {
            return 9; // N/A
        }

        $alarm = 0;
        foreach ($tags as $key => $tag) {
            if ($data[$tag] != 0) {
                $alarm = 1;
                break;
            }
        }

        return $alarm;
    }

    protected function getUreaLevel($project)
    {
        return 0;
        $tagName = "ML060_o";

        $projectNumber = $project->projectNumber;
        $url = "https://safetypower.net/api/1.0/stations/$projectNumber/tags";

        $res = $this->httpGet($url);
        $json = json_decode($res);

        if ($json) {
            foreach ($json->payload as $payload) {
                if ($payload->name == $tagName) {
                    return $payload->laststate->value;
                }
            }
        }

        return 0;
    }

    protected function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $accessToken = "QJbs0soFrj3IukQQNyIAvTi0l7iLNQAtAL";

        $headers = [ "Authorization: Bearer $accessToken" ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output ;
    }
}
