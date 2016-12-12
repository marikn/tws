<?php

$config = include_once 'config.php';

$routes = [
    'method' => [
        'obtain_execution_lock',
        'release_execution_lock',
    ],
    'job' => [
        'A',
        'B',
    ]
];

$uri = $_SERVER['REQUEST_URI'];

$uriParts = explode('/', $uri);

if (empty($uriParts) || count($uriParts) > 3 || !in_array($uriParts['1'], $routes['method']) || !in_array($uriParts['2'], $routes['job'])) {
    http_response_code(404);
    include_once '../src/error/404.php';
    exit;
}

header('Content-Type: application/json');

$method = $uriParts['1'];
$job    = $uriParts['2'];

$controller = new \Tws\Controller\APIController($config);

switch ($method) {
    case 'obtain_execution_lock':
        echo $controller->obtainExecutionLockAction($job);
        break;
    case 'release_execution_lock':
        echo $controller->releaseExecutionLockAction($job);
        break;
}