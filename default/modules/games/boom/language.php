<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

echo json_encode(array(
    'maintenance'    => 'بازی در حال بروزرسانی است',
    'loading'        => 'در حال بارگذاری',
    'connecting'     => 'در حال اتصال به سرور',
    'disconnected'   => 'اتصال قطع شد',
    'connect_again'  => 'اتصال دوباره',
    'exit'           => 'خروج',
    'cancel'         => 'لغو',
    'ok'             => 'تأیید',
    'close'          => 'بستن',
    'top_players'    => 'برترین بازیکنان',
    'start_to_play'  => 'شروع بازی',
    'how_to_play'    => 'چطور بازی کنم؟',
    'tab_history'    => 'تاریخچه',
    'tab_chat'       => 'چت',
    'tab_boxes'      => 'جعبه‌ها',
    'tab_topplayers' => 'برترین‌ها',
    'tit_user'       => 'کاربر',
    'tit_click'      => 'کلیک',
    'tit_amount'     => 'مبلغ',
    'tit_time'       => 'زمان',
    'click_start'    => 'برای شروع کلیک کنید',
    'game_stop'      => 'همه دکمه‌ها در {time} تمام می‌شوند'
), JSON_UNESCAPED_UNICODE);
exit;