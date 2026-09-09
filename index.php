<?php
define('ENVIRONMENT', 'develope');
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', '0');
session_start();
error_reporting(false);
ini_set('display_errors', 'off');
!defined('DS') ? define('DS', str_replace(array('/', '\\'), array('/', '/'), DIRECTORY_SEPARATOR)) : '';
!defined('FCPATH') ? define('FCPATH', dirname(__FILE__) . DS) : '';
!defined('APPPATH') ? define('APPPATH', FCPATH . 'TkStarApplication' . DS) : '';
!defined('VIEWPATH') ? define('VIEWPATH', APPPATH . 'views' . DS) : '';
!defined('MAIL_SENDER_NAME') ? define('MAIL_SENDER_NAME', 'i-winner') : '';
!defined('MAIL_SENDER_SERVER') ? define('MAIL_SENDER_SERVER', 'mail.i-winner.tk') : '';
!defined('MAIL_SENDER_USERNAME') ? define('MAIL_SENDER_USERNAME', 'info@i-winner.tk') : '';
!defined('MAIL_SENDER_PASSWORD') ? define('MAIL_SENDER_PASSWORD', 'x6Wdz8oRzf') : '';
!defined('VENDOR_DIR') ? define('VENDOR_DIR', FCPATH . 'TkStarVendor' . DS) : '';
!defined('BASE_URL') ? define('BASE_URL', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME']) : '';
!defined('SELF') ? define('SELF' , pathinfo(__FILE__, PATHINFO_BASENAME)) : '';
!defined('BASEPATH') ? define('BASEPATH' , str_replace(array('/', '\\'), array('/', '/'), VENDOR_DIR . 'codeigniter/framework/system') . DS) : '';
!defined('SYSDIR') ? define('SYSDIR', 'system') : '';
require_once(VENDOR_DIR . 'autoload.php');
require_once(APPPATH . 'config/eloquent.php');
require_once(BASEPATH . 'core/CodeIgniter.php');
?>