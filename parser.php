<?php
require_once './app/Services/Split.php';

use App\Services\Split;

$parseText = file_get_contents('./files/access_log.txt');
// Разбиение содержимого в массив строк
$rows = explode("\n", $parseText);

$statistics = [
    'views' => 0,
    'urls' => 0,
    'traffic' => 0,
    'crawlers' => [
        'Google' => 0,
        'Bing' => 0,
        'Baidu' => 0,
        'Yandex' => 0,
    ],
    'statusCodes' => [
        '200' => 0,
        '301' => 0,
    ],
];
$uniqueUrls = [];

foreach ($rows as $row) {
    $currentLogEntry = Split::lineSplit($row);
    $statistics['views']++;
    if (!in_array($currentLogEntry['path'], $uniqueUrls)) {
        $uniqueUrls[] = $currentLogEntry['path'];
    }
    if ((int)$currentLogEntry['status'] < 300 || (int)$currentLogEntry['status'] >= 400) {
        $statistics['traffic'] += (int)$currentLogEntry['bytes'];
    }
    if ((int)$currentLogEntry['status'] == 200) {
        (int)$statistics['statusCodes']['200']++;
    } elseif ((int)$currentLogEntry['status'] == 301) {
        (int)$statistics['statusCodes']['301']++;
    }

    $agentName = Split::agentName($currentLogEntry['agent']);

    if ($agentName !== null && isset($statistics['crawlers'][$agentName])) {
        $statistics['crawlers'][$agentName]++;
    }

}

$statistics['urls'] = count($uniqueUrls);

(new App\Services\Split)->debug($statistics);