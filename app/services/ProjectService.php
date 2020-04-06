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

    public function getGeneratorStatus($data)
    {
        if (isset($data['M_Start_Auto'])) {
            return $data['M_Start_Auto'];
        }
        if (isset($data['Gen_Total_kW'])) {
            return $data['Gen_Total_kW'] == 0;
        }
        return 1;
    }

    public function getEmergencyStart($data)
    {
        if (isset($data['Emergency_Mode'])) {
            return $data['Emergency_Mode'];
        }
        return 'N/A';
    }

    public function getGeneratorPower($data)
    {
        if (isset($data['M_Gen_real_enrg'])) {
            return $data['M_Gen_real_enrg'];
        }
        if (isset($data['Gen_Total_kW'])) {
            return $data['Gen_Total_kW'];
        }
        if (isset($data['gen_total_kw'])) {
            return $data['gen_total_kw'];
        }
        if (isset($data['P3X'])) {
            return $data['P3X'];
        }
        return 0;
    }

    public function getStoreLoad($data)
    {
        if (isset($data['M_Total_Main_po'])) {
            return $data['M_Total_Main_po'];
        }
        if (isset($data['Util_kW'])) {
            return $data['Util_kW'];
        }
        if (isset($data['P3Y'])) {
            return $data['P3Y'];
        }
        return 0;
    }

    public function getGeneratorBreakerStatus($data)
    {
        if (isset($data['M_SLD_Gen_Brkr52GAux'])) {
            return $data['M_SLD_Gen_Brkr52GAux'];
        }
        if (isset($data['Gen_CB_Pos'])) {
            return $data['Gen_CB_Pos'] == 0;
        }
        if (isset($data['ROW78'])) {
            // P35 - St. Thomas
            // Bit7 = 52U (1=closed)
            // Bit6 = 52G1 (1=closed)
            // Bit5 = 52G2 (1=closed)
            return (intval($data['ROW78']) & 0x40) == 0;
        }
        return 'N/A';
    }

    public function getMainBreakerStatus($data)
    {
        if (isset($data['M_SLD_Brkr52MAux'])) {
            return $data['M_SLD_Brkr52MAux'];
        }
        if (isset($data['Util_CB_Pos'])) {
            return $data['Util_CB_Pos'] == 0;
        }
        if (isset($data['ROW78'])) {
            // P35 - St. Thomas
            // Bit7 = 52U (1=closed)
            // Bit6 = 52G1 (1=closed)
            // Bit5 = 52G2 (1=closed)
            return (intval($data['ROW78']) & 0x80) == 0;
        }
        return 'N/A';
    }

    public function getWebRelayInfo($projectId)
    {
        $sql = "SELECT * FROM web_relay_info WHERE project_id=$projectId";
        $info = $this->db->fetchOne($sql);
        return $info;
    }

    public function saveWebRelayLog($info)
    {
        $this->db->insertAsDict('web_relay_log', [
            'user_id'      => $info['user_id'],
            'user_name'    => $info['user_name'],
            'user_ip'      => $info['user_ip'],
            'project_id'   => $info['project_id'],
            'project_name' => $info['project_name'],
            'relay1_state' => $info['relay1_state'],
            'relay2_state' => $info['relay2_state'],
            'relay3_state' => $info['relay3_state'],
            'relay4_state' => $info['relay4_state'],
        ]);
    }
}
