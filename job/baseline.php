<?php

include __DIR__ . '/../public/init.php';

#$di = \Phalcon\Di::getDefault();
#
#$baselineService = $di->get('baselineService');
#$baselineService->generateHourlyLoad(); // yesterday
#$baselineService->generateBaseline(); // yesterday

$di = \Phalcon\Di::getDefault();
$db = $di->get('db');

$db->execute("TRUNCATE TABLE hourly_load");
$db->execute("TRUNCATE TABLE baseline_history");

$baselineService = $di->get('baselineService');

$date = date('Y-m-d', strtotime('-1 days'));
$date = '2022-08-15';

$baselineService->generateHourlyLoad($date);
$baselineService->generateBaseline($date);

#$dates = $baselineService->getValidDates();
#
#echo "Generate Hourly Load\n";
#for ($d=78; $d>0; $d--) {
#    $date = date('Y-m-d', strtotime("-$d days"));
#    echo $date, "\n";
#    $baselineService->generateHourlyLoad($date);
#}
#
#echo "Generate Baseline\n";
#for ($d=30; $d>0; $d--) {
#    $date = date('Y-m-d', strtotime("-$d days"));
#    echo $date, "\n";
#    $baselineService->generateBaseline($date);
#}
