<?php


/* DEBUG */
define('DEBUG_MODE', 1);
//
// or
// 
// 
//if($_SERVER['REMOTE_ADDR'] == '67.185.164.180'){
//    define('DEBUG_MODE', 1);
//}else{
//    define('DEBUG_MODE', 0);
//}

/* END_DEBUG */


//ini_set('memory_limit', '256M');
//date_default_timezone_set('Asia/Yekaterinburg');

define('APP_DIR', realpath(__DIR__ . '/../application'));
define("ROOT_DIR", realpath(__DIR__ . '/..'));
define('PUBLIC_DIR', realpath(__DIR__));


require_once APP_DIR.'/bootstrap.php';