<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class SnapshotService extends Injectable
{
    public function load()
    {
        $nothing = [
            'rows'  => [],
            'total' => [
                'current_power' => '',
                'project_size_ac' => '',
                'average_irradiance' => '',
                'performance' => '',
            ]
        ];

        $result = $this->db->fetchAll("SELECT * FROM snapshot");

        $auth = $this->session->get('auth');
        if (!is_array($auth)) {
            return $nothing; // if user not logged in, display nothing
        }

        $userProjects = $this->userService->getUserProjects($auth['id']);

        $data = [];
        foreach ($result as $key => $val) {
            if (!in_array($val['project_id'], $userProjects)) {
                continue; // current user doesn't have permission to the project
            }

            $data[$key] = $result[$key];
        }

        return [ 'rows' => $data, 'total' => $total ];
    }

    public function generate()
    {
        echo 'Snapshot generating...';

        $projects = $this->projectService->getAll();

        foreach ($projects as $project) {
            $id = $project->id;
            $name = $project->name;
            $sizeAC = $project->capacityAC;

            $GCPR                 = $this->getGCPR($project);
            $currentPower         = $this->getCurrentPower($project);
            $irradiance           = $this->getIrradiance($project);
            $temperature          = $this->getTemperature($project);
            $invertersGenerating  = $this->getGeneratingInverters($project, $currentPower, $irradiance);
            $devicesCommunicating = $this->getCommunicatingDevices($project);
            $lastCom              = $this->getLastCom($project);

            $sql = "REPLACE INTO snapshot SET"
                 . " project_id = $id,"
                 . " project_name = '$name',"
                 . " project_size_ac = '$sizeAC',"
                 . " GCPR = '$GCPR',"
                 . " current_power = '$currentPower',"
                 . " irradiance = '$irradiance',"
                 . " temperature = '$temperature',"
                 . " inverters_generating = '$invertersGenerating',"
                 . " devices_communicating = '$devicesCommunicating',"
                 . " last_com = '$lastCom'";

            $this->db->execute($sql);
        }
    }
}
