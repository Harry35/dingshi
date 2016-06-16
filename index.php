<?php
namespace iDeliveryFood;
//引入配置文件
include_once __DIR__.'/config.php';
//引入自动载入函数
include_once __DIR__.'/autoloader.php';
//调用自动载入函数
AutoLoader::register();

require_once __DIR__.'/vendor/autoload.php';


session_start();

$params = explode('/', $_GET['q']);
$params[0] = ucfirst(strtolower($params[0]));
$params[0] .= 'Controller';

$contoller = 'iDeliveryFood\\controllers\\'.$params[0];

$resto = new $contoller;
if (isset($params[2])) {
    $resto->$params[1]($params[2]);
} else {
    $resto->$params[1]();
}

