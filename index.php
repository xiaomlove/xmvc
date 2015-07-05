<?php

define('ROOT_PATH', __DIR__.'/');
define('APP_PATH', ROOT_PATH.'application/');

define('DEBUG', true);
define('NO_LOG_AJAX', true);
define('STOP_REDIRECT', false);

define('MODE', 'RELEASE');//线上环境定义该常量，引入不同的配置和js
// define('NO_CACHE', true);//不缓存
// define('FLUSH_CACHE', true);//清除缓存


if (defined('MODE') && MODE === 'RELEASE')
{
	$config = require './application/protect/config/config-release.php';//不带xmvc
}
else 
{
	$config = require './application/protect/config/config.php';//带xmvc
}

require './framework/App.php';
framework\App::run($config);


/**
 * 已经设置缓存的内容
 * 1、Model中的SHOW_COLUMNS
 * 2、UserModel中的getRules
 */