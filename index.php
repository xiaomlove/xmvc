<?php

define('APP_PATH', __DIR__.'/application/');
define('DEBUG', true);
define('NO_LOG_AJAX', true);
define('STOP_REDIRECT', false);

//define('MODE', 'RELEASE');//线上环境定义该常量，引入不同的配置和js




if (defined('MODE') && MODE === 'RELEASE')
{
	$config = require './application/protected/config/config-release.php';
}
else 
{
	$config = require './application/protected/config/config.php';
}
require './framework/setup.php';
App::run($config);