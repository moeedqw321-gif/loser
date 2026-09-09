<?php
if(!defined('BASEPATH')){
	exit('TkStar Error !');
}
$config['base_url'] = 'http://' . $_SERVER['SERVER_NAME'];
$config['index_page'] = '';
$config['uri_protocol'] = 'AUTO';
$config['header_js'] = array();
$config['header_css'] = array();
$config['installed'] = false;
$config['url_suffix'] = '';
$config['language'] = 'persian';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = false;
$config['subclass_prefix'] = 'MY_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['composer_autoload'] = realpath(APPPATH . '../vendor/autoload.php');
$config['allow_get_array'] = true;
$config['enable_query_strings'] = false;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['cache_path'] = 'd';
$config['encryption_key'] = '371f5ac9be2a19837e86f0c7043fbb8c';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'Landa';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = BASEPATH . 'cache/sess';
$config['sess_match_ip'] = false;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = false;
$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = false;
$config['global_xss_filtering'] = false;
$config['csrf_protection'] = false;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['compress_output'] = false;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = false;
$config['proxy_ips'] = '';
?>