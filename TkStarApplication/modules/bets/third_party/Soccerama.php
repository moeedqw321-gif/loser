<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$basePaths = [
    dirname(__FILE__) . '/../modules/bets/third_party/soccerama/BaseSoccerama.php',
    dirname(__FILE__) . '/../../modules/bets/third_party/soccerama/BaseSoccerama.php',
    (defined('APPPATH') ? APPPATH : '') . 'modules/bets/third_party/soccerama/BaseSoccerama.php',
    (defined('FCPATH') ? FCPATH : '') . 'application/modules/bets/third_party/soccerama/BaseSoccerama.php',
    (defined('FCPATH') ? FCPATH : '') . 'modules/bets/third_party/soccerama/BaseSoccerama.php',
];

$loaded = false;
foreach ($basePaths as $p) {
    if ($p && file_exists($p)) {
        require_once $p;
        $loaded = true;
        break;
    }
}
if (!$loaded) {
    // try relative from common locations
    $try = realpath(dirname(__FILE__) . '/../third_party/soccerama/BaseSoccerama.php');
    if ($try && file_exists($try)) {
        require_once $try;
        $loaded = true;
    }
}
if (!$loaded) {
    show_error('Soccerama BaseSoccerama.php not found. Place third_party/soccerama correctly.');
}

class Soccerama extends BaseSoccerama {
    public function __construct() {
        parent::__construct();
    }
}
