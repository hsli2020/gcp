<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class SnapshotService extends Injectable
{
    public function load()
    {
        $sql = "SELECT *, CONVERT_TZ(time_utc, 'UTC', 'America/Toronto') AS time FROM snapshot";
        $result = $this->db->fetchAll($sql);

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
            if ($data[$tag] != 0) {
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
            foreach ($json->payload as $payload) {
                if ($payload->name == $tagName) {
                    return $payload->laststate->value;
                }
            }
        }

        return 0;
    }
}
