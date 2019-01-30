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
}
