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
}
