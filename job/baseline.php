<?php

include __DIR__ . '/../public/init.php';

$di = \Phalcon\Di::getDefault();

$baselineService = $di->get('baselineService');
$baselineService->generateHourlyLoad(); // yesterday
$baselineService->generateBaseline(); // yesterday
