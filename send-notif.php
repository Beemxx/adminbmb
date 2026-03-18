<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['error'=>'Method not allowed']); exit; }

$input  = json_decode(file_get_contents('php://input'), true);
$appId  = $input['app_id']  ?? '';
$apiKey = $input['api_key'] ?? '';
$title  = $input['title']   ?? '';
$msg    = $input['message'] ?? '';
$url    = $input['url']     ?? null;
$type   = $input['type']    ?? 'announcement';

if (!$appId || !$apiKey || !$title || !$msg) {
    echo json_encode(['error' => 'Parameter tidak lengkap']); exit;
}

$body = [
    'app_id'            => $appId,
    'included_segments' => ['All'],
    'headings'          => ['en' => $title],
    'contents'          => ['en' => $msg],
    'data'              => ['type' => $type],
];
if ($url) $body['url'] = $url;

$ch = curl_init('https://onesignal.com/api/v1/notifications');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Key ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode($body),
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
echo $response;
