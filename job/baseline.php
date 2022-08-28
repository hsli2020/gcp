<?php

include __DIR__ . '/../public/init.php';

$di = \Phalcon\Di::getDefault();
$db = $di->get('db');

$db->execute("TRUNCATE TABLE hourly_load");
$db->execute("TRUNCATE TABLE baseline_history");

$baselineService = $di->get('baselineService');

$date = date('Y-m-d', strtotime('-1 days'));

$baselineService->generateHourlyLoad($date);
$baselineService->generateBaseline($date);
