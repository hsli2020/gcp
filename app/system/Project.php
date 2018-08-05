<?php

namespace App\System;

class Project
{
    protected $id;
    protected $projectNumber;
    protected $fundName;
    protected $projectSize;
    protected $siteName;
    protected $address;
    protected $storeNumber;
    protected $operationMode;
    protected $primaryIP;
    protected $backupIP;
    protected $ftpdir;

    protected $devices = [];  // all devices

    public function __construct($info)
    {
        $this->id            = $info['id'];
        $this->name          = $info['site_name'];  // alias of siteName
        $this->projectNumber = $info['project_number'];
        $this->fundName      = $info['fund_name'];
        $this->projectSize   = $info['project_size'];
        $this->siteName      = $info['site_name'];
        $this->address       = $info['address'];
        $this->storeNumber   = $info['store_number'];
        $this->operationMode = $info['operation_mode'];
        $this->primaryIP     = $info['primary_ip'];
        $this->backupIP      = $info['backup_ip'];
        $this->ftpdir        = $info['ftpdir'];
    }

    public function initDevices($info)
    {
        $code = $info['devcode'];
        $device = new Device($this, $info);
        $this->devices[$code] = $device;
    }

    protected function getDb()
    {
        $di = \Phalcon\Di::getDefault();
        return $di->get('db');
    }

    public function getDevices()
    {
        return $this->devices;
    }

    public function __get($prop)
    {
        if (isset($this->$prop)) {
            return $this->$prop;
        }

        return null;
    }

    public function getLatest()
    {
        $id = $this->id;
        $sql = "SELECT * FROM latest WHERE project_id=$id";
        $row = $this->getDb()->fetchOne($sql);
        return $row ? json_decode($row['data'], true) : [];
    }

    public function getSnapshot()
    {
        $id = $this->id;
        $sql = "SELECT * FROM snapshot WHERE project_id=$id";
        $row = $this->getDb()->fetchOne($sql);
        return $row ? $row : [];
    }

    public function getAlarms()
    {
        $id = $this->id;
        $sql = "SELECT project_id, devcode, tagname, value, description,
                       CONVERT_TZ(start_time, 'UTC', 'EST') AS start_time,
                       CONVERT_TZ(end_time,   'UTC', 'EST') AS end_time
                  FROM alarm WHERE project_id=$id ORDER BY id DESC LIMIT 200";
        $rows = $this->getDb()->fetchAll($sql);
        return $rows;
    }

    public function export($params)
    {
        $filename = BASE_DIR.'/tmp/export-'.str_replace(' ', '-', $this->name).'-'.date('Ymd-His').'.csv';

        $file = fopen($filename, 'w');

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('1 day'));

        $startTime = empty($params['start-time']) ? $today    : $params['start-time'];
        $endTime   = empty($params['end-time'])   ? $tomorrow : $params['end-time'];

        if ($startTime == $endTime) {
            $endTime = date('Y-m-d', strtotime('1 day', strtotime($startTime)));
        }

        fputs($file, 'Project:    ' .$this->name. PHP_EOL);
        fputs($file, 'Start Time: ' .$startTime. PHP_EOL);
        fputs($file, 'End Time:   ' .$endTime. PHP_EOL. PHP_EOL);

        foreach ($this->devices as $device) {
            $device->export($file, $startTime, $endTime);
        }

        fclose($file);

        return $filename;
    }
}
