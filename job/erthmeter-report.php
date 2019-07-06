<?php

include __DIR__ . '/../public/init.php';

$di = \Phalcon\Di::getDefault();

$reportService = $di->get('reportService');

$d = date('d');

if ($d < 10) {
    $lastmonth = strtotime('-1 month');
    $y = date('Y', $lastmonth);
    $m = date('m', $lastmonth);
    $start = 1;
    $end = date('t', $lastmonth);

    $reportService->getErthmeterReport($y, $m, $start, $end);
    $reportService->send();
}

if ($d >= 20 && $d < 30) {
    $y = date('Y');
    $m = date('m');
    $start = 1;
    $end = 15;

    $reportService->getErthmeterReport($y, $m, $start, $end);
    $reportService->send();
}