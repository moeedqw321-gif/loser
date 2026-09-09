<?php
// هیچ خروجی قبل از این نباشد
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$socket = 'ws://127.0.0.1:2222';
$token  = 'local-boom-token-'.md5('boom'.date('Ymd'));

echo json_encode(array(
    'socket' => array($socket),
    'token'  => $token
), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;