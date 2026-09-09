<?php
use Illuminate\Database\Capsule\Manager as Capsule;
$db = array();
require_once(APPPATH . 'config/database.php');
$capsule = new Capsule;
if($db[$active_group]['dbdriver'] == 'mysqli'){
	$db[$active_group]['dbdriver'] = 'mysql';
}
$capsule->addConnection(array('driver' => $db[$active_group]['dbdriver'], 'host' => $db[$active_group]['hostname'], 'database' => $db[$active_group]['database'], 'username' => $db[$active_group]['username'], 'password' => $db[$active_group]['password'], 'charset' => $db[$active_group]['char_set'], 'collation' => $db[$active_group]['dbcollat'], 'prefix' => $db[$active_group]['dbprefix']));
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();
?>