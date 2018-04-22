<?php

// See examples at https://safetypower.net/?q=api/explorer
//
// (all stations, general information)
// $url = "https://safetypower.net/api/1.0/stations";

$sites = [
    2496771 => "Richmond Hill #1028",
    2497284 => "Markham #1032",
    2497797 => "Oakville #1024",
    2550636 => "Winston Churchill #1080",
    2551149 => "Milton #2810",
    2551662 => "Newmarket #1018",
    2552175 => "Glen Erin #1011",
    2552688 => "South Orleans Ottawa #1071",
    2553201 => "Oshawa #1043",
    2553714 => "Whitby #1058",
    2554227 => "Highland Hills #1000",
    2554740 => "Aurora #1030",
];

foreach ($sites as $id => $name) {
    $url = "https://safetypower.net/api/1.0/stations/$id/tags";
    echo $id, ' => ', $name, PHP_EOL;

    $res = httpGet($url);
    $json = json_decode($res);

    foreach ($json->payload as $payload) {
        echo date('Y-m-d H:i:s ', $payload->laststate->timestamp);
        echo $payload->name, '=', $payload->laststate->value, PHP_EOL;
    }

    break;
}

function httpGet($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $accessToken = "QJbs0soFrj3IukQQNyIAvTi0l7iLNQAtAL";

    $headers = [ "Authorization: Bearer $accessToken" ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $output = curl_exec($ch);
    curl_close($ch);

    return $output ;
}
