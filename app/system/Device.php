<?php

namespace App\System;

abstract class Device
{
    protected $project;
    protected $type;
    protected $code;
    protected $table;
    protected $model;

    public function __construct($project, $info)
    {
        $this->project   = $project;
        $this->type      = $info['type'];
        $this->code      = $info['devcode'];
        $this->table     = $info['table'];
        $this->model     = $info['model'];
        $this->reference = $info['reference'];
    }

    public function __toString()
    {
       #return 'P' .$this->project->id. ' ' .$this->type. ' ' .$this->code;
        return $this->type. ' ' .$this->code. ' of Project ' .$this->project->name;
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
        return $this->table;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getDeviceTable()
    {
        return 'p'.$this->project->id.'_'.
               str_replace('-', '_', $this->code).'_'.
               strtolower($this->type);
    }

    public function getTableColumns()
    {
        $table = $this->getTable();
        $columns = $this->getDb()->fetchAll("DESC $table");
        return array_column($columns, 'Field');
    }
}
