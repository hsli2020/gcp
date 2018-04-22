<?php

namespace App\System;

class Project
{
    protected $id;
    protected $name;
    protected $ftpdir;

    protected $devices   = [];  // all devices

    public function __construct($info)
    {
        $this->id     = $info['id'];
        $this->name   = $info['name'];
        $this->ftpdir = $info['ftpdir'];
    }

    public function initDevices($info)
    {
        $type = $info['type'];
        $code = $info['devcode'];
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
}
