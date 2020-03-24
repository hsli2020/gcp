<?php

function toLocaltime($timeStr)
{
    $date = new \DateTime($timeStr, new \DateTimeZone('UTC'));
    $date->setTimezone(new \DateTimeZone('America/Toronto'));
    return $date->format('Y-m-d H:i:s');
}

// $now = date('Y-m-d H:i:s');
// convertTime($now, $from, $to);
// changeTimezone($now, 'America/Toronto', 'UTC');
// changeTimezone($now, 'America/Toronto', 'EST');

function changeTimezone($time, $from, $to)
{
    $date = new \DateTime($time, new \DateTimeZone($from));
    $date->setTimezone(new \DateTimeZone($to));
    return $date->format('Y-m-d H:i:s');
}

// Another implementation
function convertTime($time, $from, $to)
{
    // $from='UTC';
    // $to='America/New_York';
    // $date=date($time);

    $default = date_default_timezone_get();

    date_default_timezone_set($from);
    $newDatetime = strtotime($time);

    date_default_timezone_set($to);
    $format = 'Y-m-d H:i:s';
    $newDatetime = date($format, $newDatetime);

    date_default_timezone_set($default);

    return $newDatetime;
}
