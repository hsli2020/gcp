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

    public function export($file, $start, $end)
    {
        $table = $this->getTable();

        # CONVERT_TZ(time_utc, 'UTC', 'America/Toronto')

        $sql = "SELECT *
                  FROM $table
                 WHERE time_utc >= CONVERT_TZ('$start', 'America/Toronto', 'UTC') AND
                       time_utc <  CONVERT_TZ('$end',   'America/Toronto', 'UTC') AND error=0";

        $data = $this->getDb()->fetchAll($sql);

        fputcsv($file, $this->getTableColumns());

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fputs($file, PHP_EOL);
    }
}
