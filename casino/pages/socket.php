<?php
$_functions_dir=str_replace(array("pages/socket.php", "pages\socket.php"), array(""), realpath(__FILE__));
require_once($_functions_dir."config.php");
class ClientsCore {
	public static $dbDetails;
	public static $clients;
}
!defined('BASEPATH') ? define('BASEPATH', 'TkStarLoader') : '';
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once(__DIR__ . '/../../TkStarApplication/config/database.php');
!is_array(ClientsCore::$dbDetails) ? ClientsCore::$dbDetails = $db : '';
$main_connection = new mysqli(ClientsCore::$dbDetails['default']['hostname'], ClientsCore::$dbDetails['default']['username'], ClientsCore::$dbDetails['default']['password'], ClientsCore::$dbDetails['default']['database']);
$main_connection->set_charset('UTF8');
$host = '127.0.0.1';
$port = 2222;
$class_socket = new PHPWebSocket();
$class_socket->bind('message', 'wsOnMessage');
$class_socket->bind('open', 'wsOnOpen');
$class_socket->bind('close', 'wsOnClose');
$class_socket->wsStartServer($host, $port);
function wsOnMessage($requester, $message){
	global $class_socket, $main_connection;
	$explode_message = explode('|', $message);
	switch($explode_message[0]){
		case('socket_register'): $class_socket->wsSend($requester, socket_register($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_login'): $class_socket->wsSend($requester, socket_login($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_editProfile'): $class_socket->wsSend($requester, socket_editProfile($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_editPassword'): $class_socket->wsSend($requester, socket_editPassword($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_roomCreate'): $class_socket->wsSend($requester, socket_roomCreate($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_roomCreate2'): $class_socket->wsSend($requester, socket_roomCreate2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_roomJoin'): $class_socket->wsSend($requester, socket_roomJoin($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_roomJoin2'): $class_socket->wsSend($requester, socket_roomJoin2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_getLastChanges'): $class_socket->wsSend($requester, socket_getLastChanges($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_getLastChanges2'): $class_socket->wsSend($requester, socket_getLastChanges2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_showCards'): $class_socket->wsSend($requester, socket_showCards($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_showKing'): $class_socket->wsSend($requester, socket_showKing($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_showVerdict'): $class_socket->wsSend($requester, socket_showVerdict($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_removeFirstTwoCards'): $class_socket->wsSend($requester, socket_removeFirstTwoCards($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_chooseCards'): $class_socket->wsSend($requester, socket_chooseCards($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_gameStartCards'): $class_socket->wsSend($requester, socket_gameStartCards($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_sendCard'): $class_socket->wsSend($requester, socket_sendCard($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_sendCard2'): $class_socket->wsSend($requester, socket_sendCard2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_requestForDouble'): $class_socket->wsSend($requester, socket_requestForDouble($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_requestForDouble2'): $class_socket->wsSend($requester, socket_requestForDouble2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_changeDoubleStatus'): $class_socket->wsSend($requester, socket_changeDoubleStatus($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_changeDoubleStatus2'): $class_socket->wsSend($requester, socket_changeDoubleStatus2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_clearLastChange'): $class_socket->wsSend($requester, socket_clearLastChange($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_editBankDetails'): $class_socket->wsSend($requester, socket_editBankDetails($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_gameClose'): $class_socket->wsSend($requester, socket_gameClose($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_gameClose2'): $class_socket->wsSend($requester, socket_gameClose2($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('socket_pokerJoin'): $class_socket->wsSend($requester, socket_pokerJoin($message . '|' . long2ip($class_socket->wsClients[$requester][6]))); break;
		case('crash_new_round'):
			$message = explode('|', $message);
			$message[1] = safe_number($message[1]);
			if(count(ClientsCore::$clients) <= 1){
				$main_connection->query("UPDATE `crash_table` SET `finish_time` = '" . time() . "', `play` = '0'");
			}
			$main_connection->query("UPDATE `crash_table` SET `time` >= '" . strtotime('-90 seconds') . "', `play` = '0'");
			$query_check_lastCrash = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 1");
			$row_check_lastCrash = $query_check_lastCrash->fetch_array(MYSQLI_ASSOC);
			settype($row_check_lastCrash['play'], 'int');
			if($row_check_lastCrash['play'] == 0){
				$first_array = array(array(0, 1), array(0, 2), array(1, 1), array(0, 1.5), array(1, 1.5), array(1, 2), array(0, 2), array(0, 5), array(1, 3), array(1.1, 2.25), array(1.5, 2.5), array(5, 10), array(2, 3), array(2, 75), array(1, 3.5), array(2, 2.75), array(2, 2.65), array(3, 7), array(1, 1.85), array(1, 1.90), array(1, 1), array(2, 2.35), array(0, 0), array(1, 4), array(1, 1), array(0, 1), array(1, 1.25), array(1, 1.35), array(1, 2), array(2, 2.80), array(1, 2.75), array(1, 5.25), array(0, 0), array(0, 2), array(0, 0), array(1, 1.15), array(1, 1.75), array(2, 2.90), array(1, 1.45), array(1, 3), array(1, 2), array(1, 3), array(0, 0));
				$selected_array = $first_array[array_rand($first_array)];
				$random_number = round(float_rand($selected_array[0], $selected_array[1]), 2);
				$random_number = $random_number < 1 ? 0 : $random_number;
				$names = array('King Mamad', 'AmirAli', 'N@srin', 'R3z@', 'شادی', 'Amita', 'مملی', 'فراز', 'Alexi', 'چقدر بد شانسم', 'Limoozin', 'Jaguare', 'پروانه', 'Falko', 'ShadowFiend', 'Cris7', 'یوزارسیف', 'ستاره', 'MaHaN', 'فرشاد');
				$fake_user_count = rand(5, 10);
				$array_fake_users = array();
				for($i = 0; $i <= $fake_user_count; $i++){
					$random_index = rand(1, count($names));
					if(empty($names[$random_index])){
						continue;
					}else{
						$random_price = ceil(rand(500, 250000));
						$random_price_string = (string)$random_price;
						$random_price_string_half = floor(strlen($random_price) / 2);
						$random_price = mb_substr($random_price_string, 0, $random_price_string_half, 'utf-8');
						for($j = 0; $j < $random_price_string_half; $j++){
							$random_price = $random_price . '0';
						}
						$random_number = rand(1, 2);
						$random_float = float_rand($random_number, ($random_number + 1));
						$array_fake_users['10000000000' . $i] = array('username' => $names[$random_index], 'price' => $random_price, 'odd' => '0', 'win' => 0, 'fake' => 'true', 'fake_last_odd' => round($random_float, 2));
						unset($names[$random_index]);
					}
				}
				ksort($array_fake_users);
				$main_connection->query("INSERT INTO `crash_table` (`id`, `last_odd`, `time`, `finish_time`, `play`, `users`) VALUES (NULL, '" . $random_number . "', '" . time() . "', '0', '1', '" . json_encode($array_fake_users, JSON_UNESCAPED_UNICODE) . "')");
				$insert_id = $main_connection->insert_id;
				$history_request = '';
				$query_history = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 50");
				while($row_history = $query_history->fetch_array(MYSQLI_ASSOC)){
					$users = $row_history['users'];
					$users = json_decode($users, true);
					$me_in_this_crash = 'false';
					foreach($users as $user_id => $user_odd){
						if($user_id == $message[1]){
							$me_in_this_crash = 'true';
							break;
						}else{
							$me_in_this_crash = 'false';
							continue;
						}
					}
					if($me_in_this_crash == 'true'){
						if($users[$message[1]]['win'] == '2'){
							$my_sood = '<span class="text-danger">0 تومان</span>';
						}else{
							if($users[$message[1]]['win'] == '1'){
								$my_odd = $users[$message[1]]['odd'];
								$my_price = $users[$message[1]]['price'];
								$my_win_price = $my_odd * $my_price;
								$my_sood = '<span class="text-success">' . number_format($my_win_price - $my_price) . ' تومان</span>';
							}else{
								$my_sood = '-';
							}
						}
					}else{
						$my_sood = '-';
					}
					$history_request .= '<tr><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . $row_history['id'] . '</td><td style="text-align: center !important;">' . ($row_history['finish_time'] == 0 ? '-' : $row_history['last_odd']) . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? number_format($users[$message[1]]['price']) . ' تومان' : '-') . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? ($users[$message[1]]['win'] == '1' ? '<span class="text-success">' . round($users[$message[1]]['odd'], 2) . '</span' : ($users[$message[1]]['win'] == '2' ? '<span class="text-danger">' . $row_history['last_odd'] . '</span>' : '-')) : '-') . '</td><td style="text-align: center !important;">' . $my_sood . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . hash('md2', $row_history['time']) . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . ($row_history['finish_time'] == '0' ? '-' : hash('md5', $row_history['finish_time'])) . '</td></tr>';
				}
				foreach(ClientsCore::$clients as $client_id => $client_status){
					$class_socket->wsSend($client_id, 'round_start|' . $insert_id . '|' . $random_number . '|' . $history_request . '|' . json_encode($array_fake_users, JSON_UNESCAPED_UNICODE));
				}
			}
			break;
		case('crash_reduce_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = round(safe_number($message[2]));
			$message[3] = round(safe_number($message[3]));
			$message[4] = round(safe_number($message[4]));
			$main_connection->query("UPDATE `users` SET `cash` = `cash` - " . $message[1] . " WHERE `id` = '" . $message[2] . "'");
			break;
		case('check_crash_users'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$query_check_users = $main_connection->query("SELECT * FROM `crash_table` WHERE `id` = '" . $message[1] . "'");
			$row_check_users = $query_check_users->fetch_array(MYSQLI_ASSOC);
			$users = $row_check_users['users'];
			$users = json_decode($users, true);
			$new_crash_number = 'true';
			foreach($users as $user_id => $user_object){
				if(!isset($user_object['fake']) || $user_object['fake'] !== 'true'){
    $new_crash_number = 'false';
    break;
}
			}
			if($new_crash_number == 'true'){
				$random_number = round(float_rand(10, (100 + round(float_rand(0, 2), 2))), 2);
			}else{
				$first_array = array(array(0, 1), array(0, 2), array(1, 1), array(0, 1.5), array(1, 1.5), array(1, 2), array(0, 2), array(0, 5), array(1, 3), array(0, 2.25), array(1, 2.5), array(1, 10), array(1, 2), array(2, 75), array(1, 3.5), array(1, 2.75), array(1, 2.65), array(1, 7), array(1, 1.85), array(1, 1.90), array(1, 1), array(2, 2.35), array(0, 0), array(1, 4), array(1, 1), array(0, 1), array(1, 1.25), array(1, 1.35), array(1, 2), array(2, 2.80), array(1, 2.75), array(1, 5.25), array(0, 0), array(0, 2), array(0, 0), array(1, 1.15), array(1, 1.75), array(2, 2.90), array(1, 1.45), array(1, 3), array(1, 2), array(1, 3), array(0, 0));
				$selected_array = $first_array[array_rand($first_array)];
				$random_number = round(float_rand($selected_array[0], $selected_array[1]), 2);
				$random_number = $random_number < 1 ? 1.01 : $random_number;
			}
			$new_game_users = json_decode($row_check_users['users'], true);
			$main_connection->query("UPDATE `crash_table` SET `last_odd` = '" . $random_number . "' WHERE `id` = '" . $message[1] . "'");
			$history_request = '';
			$query_history = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 50");
			while($row_history = $query_history->fetch_array(MYSQLI_ASSOC)){
				$users = $row_history['users'];
				$users = json_decode($users, true);
				$me_in_this_crash = 'false';
				foreach($users as $user_id => $user_odd){
					if($user_id == $message[1]){
						$me_in_this_crash = 'true';
						break;
					}else{
						$me_in_this_crash = 'false';
						continue;
					}
				}
				if($me_in_this_crash == 'true'){
					if($users[$message[3]]['win'] == '2'){
						$my_sood = '<span class="text-danger">0 تومان</span>';
					}else{
						if($users[$message[3]]['win'] == '1'){
							$my_odd = $users[$message[3]]['odd'];
							$my_price = $users[$message[3]]['price'];
							$my_win_price = $my_odd * $my_price;
							$my_sood = '<span class="text-success">' . number_format($my_win_price - $my_price) . ' تومان</span>';
						}else{
							$my_sood = '-';
						}
					}
				}else{
					$my_sood = '-';
				}
				$history_request .= '<tr><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . $row_history['id'] . '</td><td style="text-align: center !important;">' . ($row_history['finish_time'] == 0 ? '-' : $row_history['last_odd']) . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? number_format($users[$message[3]]['price']) . ' تومان' : '-') . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? ($users[$message[3]]['win'] == '1' ? '<span class="text-success">' . round($users[$message[3]]['odd'], 2) . '</span' : ($users[$message[3]]['win'] == '2' ? '<span class="text-danger">' . $row_history['last_odd'] . '</span>' : '-')) : '-') . '</td><td style="text-align: center !important;">' . $my_sood . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . hash('md2', $row_history['time']) . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . ($row_history['finish_time'] == '0' ? '-' : hash('md5', $row_history['finish_time'])) . '</td></tr>';
			}
			$game_users = json_encode(array_values($new_game_users), JSON_UNESCAPED_UNICODE);
			foreach(ClientsCore::$clients as $client_id => $client_status){
				$class_socket->wsSend($client_id, 'new_crash_number|' . $random_number . '|' . $game_users . '|' . $history_request);
			}
			break;
		case('crash_add_wallet'):
			$message = explode('|', $message);
			$message[1] = round($message[1]);
			$message[2] = safe_number($message[2]);
			$message[3] = safe_codes($message[3]);
			$message[4] = safe_codes($message[4]);
			$query_check_crash_game = $main_connection->query("SELECT * FROM `crash_table` WHERE `id` = '" . $message[4] . "'");
			$row_check_crash_game = $query_check_crash_game->fetch_array(MYSQLI_ASSOC);
			$game_users = $row_check_crash_game['users'];
			$game_users = json_decode($game_users, true);
			$game_users[$message[2]]['odd'] = $message[3];
			$game_users[$message[2]]['win'] = '1';
			$game_users = json_encode($game_users, JSON_UNESCAPED_UNICODE);
			$main_connection->query("UPDATE `crash_table` SET `users` = '" . $game_users . "' WHERE `id` = '" . $message[4] . "'");
			$game_users = json_decode($game_users, true);
			$new_game_users = array();
			foreach($game_users as $key => $value){
				$new_game_users[] = $value;
			}
			$history_request = '';
			$query_history = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 50");
			while($row_history = $query_history->fetch_array(MYSQLI_ASSOC)){
				$users = $row_history['users'];
				$users = json_decode($users, true);
				$me_in_this_crash = 'false';
				foreach($users as $user_id => $user_odd){
					if($user_id == $message[2]){
						$me_in_this_crash = 'true';
						break;
					}else{
						$me_in_this_crash = 'false';
						continue;
					}
				}
				if($me_in_this_crash == 'true'){
					if($users[$message[2]]['win'] == '2'){
						$my_sood = '<span class="text-danger">0 تومان</span>';
					}else{
						if($users[$message[2]]['win'] == '1'){
							$my_odd = $users[$message[2]]['odd'];
							$my_price = $users[$message[2]]['price'];
							$my_win_price = $my_odd * $my_price;
							$my_sood = '<span class="text-success">' . number_format($my_win_price - $my_price) . ' تومان</span>';
						}else{
							$my_sood = '-';
						}
					}
				}else{
					$my_sood = '-';
				}
				$history_request .= '<tr><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . $row_history['id'] . '</td><td style="text-align: center !important;">' . ($row_history['finish_time'] == 0 ? '-' : $row_history['last_odd']) . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? number_format($users[$message[2]]['price']) . ' تومان' : '-') . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? ($users[$message[2]]['win'] == '1' ? '<span class="text-success">' . round($users[$message[2]]['odd'], 2) . '</span' : ($users[$message[2]]['win'] == '2' ? '<span class="text-danger">' . $row_history['last_odd'] . '</span>' : '-')) : '-') . '</td><td style="text-align: center !important;">' . $my_sood . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . hash('md2', $row_history['time']) . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . ($row_history['finish_time'] == '0' ? '-' : hash('md5', $row_history['finish_time'])) . '</td></tr>';
			}
			$game_users = json_encode(array_values($new_game_users), JSON_UNESCAPED_UNICODE);
			foreach(ClientsCore::$clients as $client_id => $client_status){
				$class_socket->wsSend($client_id, 'list_users|' . $game_users . '|' . $history_request);
			}
			$new_crash_number = 'true';
			foreach($new_game_users as $key => $value){
				if($new_crash_number['win'] != 0){
					$new_crash_number = 'false';
					break;
				}
			}
			if($new_crash_number == 'true'){
				$random_number = round(float_rand(30, (100 + round(float_rand(0, 2), 2))), 2);
				$main_connection->query("UPDATE `crash_table` SET `last_odd` = '" . $random_number . "' WHERE `id` = '" . $message[4] . "'");
				foreach(ClientsCore::$clients as $client_id => $client_status){
					$class_socket->wsSend($client_id, 'new_crash_number|' . $random_number);
				}
			}
			$main_connection->query("UPDATE `users` SET `cash` = `cash` + " . $message[1] . " WHERE `id` = '" . $message[2] . "'");
			break;
		case('crash_close_round'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_number($message[2]);
			$main_connection->query("UPDATE `crash_table` SET `finish_time` = '" . time() . "', `play` = '0' WHERE `id` = '" . $message[1] . "'");
			$query_check_crash_game = $main_connection->query("SELECT * FROM `crash_table` WHERE `id` = '" . $message[1] . "'");
			$row_check_crash_game = $query_check_crash_game->fetch_array(MYSQLI_ASSOC);
			$game_users = $row_check_crash_game['users'];
			$game_users = json_decode($game_users, true);
			foreach($game_users as $key => $index){
				if($index['win'] == '0'){
					$game_users[$key]['win'] = '2';
					$game_users[$key]['odd'] = $row_check_crash_game['last_odd'];
				}
			}
			$game_users = json_encode($game_users, JSON_UNESCAPED_UNICODE);
			$main_connection->query("UPDATE `crash_table` SET `users` = '" . $game_users . "' WHERE `id` = '" . $message[1] . "'");
			$game_users = json_decode((!empty($game_users) ? $game_users : '[]'), true);
			$new_game_users = array();
			if(is_array($game_users) OR is_object($game_users) AND !empty($game_users) AND !isset($game_users)){
				foreach($game_users as $key => $value){
					$new_game_users[] = $value;
				}
			}
			$history_request = '';
			$query_history = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 50");
			while($row_history = $query_history->fetch_array(MYSQLI_ASSOC)){
				$users = $row_history['users'];
				$users = json_decode($users, true);
				$me_in_this_crash = 'false';
				foreach($users as $user_id => $user_odd){
					if($user_id == $message[2]){
						$me_in_this_crash = 'true';
						break;
					}else{
						$me_in_this_crash = 'false';
						continue;
					}
				}
				if($me_in_this_crash == 'true'){
					if($users[$message[2]]['win'] == '2'){
						$my_sood = '<span class="text-danger">0 تومان</span>';
					}else{
						if($users[$message[2]]['win'] == '1'){
							$my_odd = $users[$message[2]]['odd'];
							$my_price = $users[$message[2]]['price'];
							$my_win_price = $my_odd * $my_price;
							$my_sood = '<span class="text-success">' . number_format($my_win_price - $my_price) . ' تومان</span>';
						}else{
							$my_sood = '-';
						}
					}
				}else{
					$my_sood = '-';
				}
				$history_request .= '<tr><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . $row_history['id'] . '</td><td style="text-align: center !important;">' . ($row_history['finish_time'] == 0 ? '-' : $row_history['last_odd']) . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? number_format($users[$message[2]]['price']) . ' تومان' : '-') . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? ($users[$message[2]]['win'] == '1' ? '<span class="text-success">' . round($users[$message[2]]['odd'], 2) . '</span' : ($users[$message[2]]['win'] == '2' ? '<span class="text-danger">' . $row_history['last_odd'] . '</span>' : '-')) : '-') . '</td><td style="text-align: center !important;">' . $my_sood . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . hash('md2', $row_history['time']) . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . ($row_history['finish_time'] == '0' ? '-' : hash('md5', $row_history['finish_time'])) . '</td></tr>';
			}
			$game_users = json_encode(array_values($new_game_users), JSON_UNESCAPED_UNICODE);
			foreach(ClientsCore::$clients as $client_id => $client_status){
				$class_socket->wsSend($client_id, 'list_users|' . $game_users . '|' . $history_request);
			}
			break;
		case('crash_add_user'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$message[3] = safe_number($message[3]);
			$query_check_crash_game = $main_connection->query("SELECT * FROM `crash_table` WHERE `id` = '" . $message[1] . "'");
			if($query_check_crash_game->num_rows <= 0){
				return false;
			}else{
				$me_details = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[3] . "'")->fetch_array(MYSQLI_ASSOC);
				$row_check_crash_game = $query_check_crash_game->fetch_array(MYSQLI_ASSOC);
				$game_users = $row_check_crash_game['users'];
				$game_users = json_decode($game_users, true);
				if(isset($game_users[$message[3]])){
					unset($game_users[$message[3]]);
				}else{
					$game_users[$message[3]] = array('username' => $me_details['first_name'] . ' ' . $me_details['last_name'], 'price' => $message[2], 'odd' => '0', 'win' => 0);
				}
				$game_users = json_encode($game_users, JSON_UNESCAPED_UNICODE);
			}
			$main_connection->query("UPDATE `crash_table` SET `users` = '" . $game_users . "' WHERE `id` = '" . $message[1] . "'");
			$game_users = json_decode($game_users, true);
			$new_game_users = array();
			foreach($game_users as $key => $value){
				$new_game_users[] = $value;
			}
			$history_request = '';
			$query_history = $main_connection->query("SELECT * FROM `crash_table` ORDER BY `id` DESC LIMIT 50");
			while($row_history = $query_history->fetch_array(MYSQLI_ASSOC)){
				$users = $row_history['users'];
				$users = json_decode($users, true);
				$me_in_this_crash = 'false';
				foreach($users as $user_id => $user_odd){
					if($user_id == $message[3]){
						$me_in_this_crash = 'true';
						break;
					}else{
						$me_in_this_crash = 'false';
						continue;
					}
				}
				if($me_in_this_crash == 'true'){
					if($users[$message[3]]['win'] == '2'){
						$my_sood = '<span class="text-danger">0 تومان</span>';
					}else{
						if($users[$message[3]]['win'] == '1'){
							$my_odd = $users[$message[3]]['odd'];
							$my_price = $users[$message[3]]['price'];
							$my_win_price = $my_odd * $my_price;
							$my_sood = '<span class="text-success">' . number_format($my_win_price - $my_price) . ' تومان</span>';
						}else{
							$my_sood = '-';
						}
					}
				}else{
					$my_sood = '-';
				}
				$history_request .= '<tr><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . $row_history['id'] . '</td><td style="text-align: center !important;">' . ($row_history['finish_time'] == 0 ? '-' : $row_history['last_odd']) . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? number_format($users[$message[3]]['price']) . ' تومان' : '-') . '</td><td style="text-align: center !important;">' . ($me_in_this_crash == 'true' ? ($users[$message[3]]['win'] == '1' ? '<span class="text-success">' . round($users[$message[3]]['odd'], 2) . '</span' : ($users[$message[3]]['win'] == '2' ? '<span class="text-danger">' . $row_history['last_odd'] . '</span>' : '-')) : '-') . '</td><td style="text-align: center !important;">' . $my_sood . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . hash('md2', $row_history['time']) . '</td><td style="text-align: center !important;" class="hidden-xs hidden-sm">' . ($row_history['finish_time'] == '0' ? '-' : hash('md5', $row_history['finish_time'])) . '</td></tr>';
			}
			$game_users = json_encode(array_values($new_game_users), JSON_UNESCAPED_UNICODE);
			foreach(ClientsCore::$clients as $client_id => $client_status){
				$class_socket->wsSend($client_id, 'list_users|' . $game_users . '|' . $history_request);
			}
			break;
		case('blackjack_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `blackjacks_table` (id, user, time, ip, price, type) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "', '" . $message[3] . "')");
				return true;
			}
			break;
		case('roulette_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$message[3] = safe_codes($message[3]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `roulettes_table` (id, user, time, ip, price, type) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "', '" . $message[3] . "')");
				return true;
			}
			break;
		case('plinko_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$message[3] = safe_codes($message[3]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `plinkoes_table` (id, user, time, ip, price) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "')");
				return true;
			}
			break;
		case('baccarat_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `baccarats_table` (id, user, time, ip, price) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "')");
				return true;
			}
			break;
		case('craps_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `craps_table` (id, user, time, ip, price) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "')");
				return true;
			}
			break;
		case('high_low_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `high_low_table` (id, user, time, ip, price) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "')");
				return true;
			}
			break;
		case('fortune_wheel_save_wallet'):
			$message = explode('|', $message);
			$message[1] = safe_codes($message[1]);
			$message[2] = safe_codes($message[2]);
			$query_check_user = $main_connection->query("SELECT * FROM `users` WHERE `id` = '" . $message[1] . "'");
			if($query_check_user->num_rows <= 0){
				return false;
			}else{
				$row_check_user = $query_check_user->fetch_array(MYSQLI_ASSOC);
				$bet_price = $message[2] - $row_check_user['cash'];
				$main_connection->query("UPDATE `users` SET `cash` = '" . $message[2] . "' WHERE `id` = '" . $message[1] . "'");
				$main_connection->query("INSERT INTO `fortune_wheel_table` (id, user, time, ip, price) VALUES (NULL, '" . $message[1] . "', '" . time() . "', '" . long2ip($class_socket->wsClients[$requester][6]) . "', '" . $bet_price . "')");
				return true;
			}
			break;
	}
}
function wsOnOpen($requester){
	global $class_socket;
	ClientsCore::$clients[$requester] = true;
}
function wsOnClose($requester, $status){
	global $class_socket;
	unset(ClientsCore::$clients[$requester]);
}
?>