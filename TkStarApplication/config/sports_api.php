<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Sports API Configuration
 * 
 * Centralized configuration for all sports data APIs.
 * API tokens should be set here. Do NOT hardcode tokens elsewhere.
 */
$config['sports_api'] = [
    'sportmonks' => [
        'base_url' => 'https://soccer.sportmonks.com/api/v2.0',
        'api_token' => 'kSLGrxDaSXfeMh5sb1xSDviFqRNXXtYjjZrL2fpLd39dHf2ibewuzCbqsJSM',
        'timeout' => 15,
        'retry_count' => 2,
        'retry_delay' => 1000,
    ],
    'soccerama' => [
        'base_url' => 'http://api.soccerama.pro/v1.2',
        'api_token' => 'A84JTajwHpBq80tga4CmfudkFn8cvbSyP0ceCozBoCiHIopBcTlv5Z1B3TQF',
        'timeout' => 15,
        'retry_count' => 2,
        'retry_delay' => 1000,
    ],
    'cache_dir' => API_DIR,
    'log_enabled' => TRUE,
    'log_file' => APPPATH . 'logs/sports_api.log',
    'max_log_size' => 5242880,
    'rate_limit' => [
        'max_requests' => 60,
        'per_seconds' => 60,
    ],
];
