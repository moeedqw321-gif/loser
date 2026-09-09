<?php
require_once 'vendor/autoload.php';
require_once 'modules/bets/third_party/soccerama/BaseSoccerama.php';
require_once 'libraries/Soccerama.php';

$s = new soccerama();
$result = $s->livescore()->now();
echo json_encode($result, JSON_UNESCAPED_UNICODE);