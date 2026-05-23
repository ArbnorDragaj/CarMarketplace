<?php
header('Content-Type: application/json');

$cacheFile = __DIR__ . '/currency_cache.json';
$cacheTime = 12 * 60 * 60;

function sendJsonResponse($data) {
    echo json_encode($data);
    exit();
}

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $cachedData = json_decode(file_get_contents($cacheFile), true);

    if (isset($cachedData['rate'])) {
        sendJsonResponse([
            'success' => true,
            'base' => 'EUR',
            'target' => 'USD',
            'rate' => $cachedData['rate'],
            'source' => 'cache'
        ]);
    }
}

$apiUrl = 'https://open.er-api.com/v6/latest/EUR';
$response = @file_get_contents($apiUrl);

if ($response === false) {
    sendJsonResponse([
        'success' => false,
        'message' => 'Currency API is not available at the moment.'
    ]);
}

$data = json_decode($response, true);

if (!isset($data['result'], $data['rates']['USD']) || $data['result'] !== 'success') {
    sendJsonResponse([
        'success' => false,
        'message' => 'Invalid response from currency API.'
    ]);
}

$rate = (float)$data['rates']['USD'];

file_put_contents($cacheFile, json_encode([
    'rate' => $rate,
    'updated_at' => date('Y-m-d H:i:s')
]));

sendJsonResponse([
    'success' => true,
    'base' => 'EUR',
    'target' => 'USD',
    'rate' => $rate,
    'source' => 'api'
]);