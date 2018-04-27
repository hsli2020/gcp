<?php

namespace App\System;

class Device
{
    protected $project;
    protected $type;
    protected $code;

    public function __construct($project, $info)
    {
        $this->project = $project;
        $this->type    = $info['type'];
        $this->code    = $info['devcode'];
    }

    public function __get($prop)
    {
        if (isset($this->$prop)) {
            return $this->$prop;
        }

        return null;
    }

    protected function getDb()
    {
        $di = \Phalcon\Di::getDefault();
        return $di->get('db');
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTable()
    {
        return 'p'.$this->project->id.'_'.str_replace('-', '_', $this->code);
    }

    public function getTableColumns()
    {
        $table = $this->getTable();
        $columns = $this->getDb()->fetchAll("DESC $table");
        return array_column($columns, 'Field');
    }
}
