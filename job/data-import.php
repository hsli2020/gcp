<?php

include __DIR__ . '/../public/init.php';

$di = \Phalcon\Di::getDefault();

$service = $di->get('importService');
$service->import();
$service->restartFtpServer();
$service->getForecastPeak();

$snapshot = $di->get('snapshotService');
$snapshot->generate();
