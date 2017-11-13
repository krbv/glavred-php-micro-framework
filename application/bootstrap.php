<?php use Glavred\Core\Loader;

define('GLAVRED_VERSION', "v0.1.1");

if(defined('DEBUG_MODE')  && DEBUG_MODE == 1){
    echo "<hr>DEBUG MODE is ON<hr>";
    ini_set('display_errors', 1);
    //error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(E_ALL);
}else{
    ini_set('display_errors', 0);
    error_reporting(0);
}

// подключаем файлы ядра
require_once APP_DIR.'/Core/Loader.php';
require_once APP_DIR.'/Core/Exceptions.php';


//composer
require_once APP_DIR.'/../vendor/autoload.php';
spl_autoload_register([new Loader(), 'loadClass']);


Loader::loadModules();
 

\Glavred\Core\Route::start(); // запускаем маршрутизатор

