<?php
if(!defined('BASEPATH')){
	exit('TkStar Error !');
}
class MY_Exceptions extends CI_Exceptions {
    function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
        log_message('debug', 'MY_Exceptions Class Initialized');
    }
    function show_404($page = '', $log_error = TRUE) {
        set_status_header('404');
		$url = 'http://' . $_SERVER['SERVER_NAME'];
		echo($this->show_error('<div style="text-align: right; direction: rtl;">وجود خطا !</div>', '<div style="text-align: right; direction: rtl;margin-right: 30px;">این صفحه وجود ندارد !<br>ممکن است خطایی رخ داده باشد یا این صفحه حذف شده باشد . در هر صورت از شما عذر میخواهیم<br><br>می توانید از <a href="' . $url . '">این لینک</a> به صفحه اصلی سایت بازگردید</a></div>', 'error_404'));
    }
}
?>