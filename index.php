<?php

define('APP_PATH', __DIR__.'/application/');
define('DEBUG', true);
define('NO_LOG_AJAX', true);

$config = require './application/config/config.php';
require './framework/setup.php';

App::run($config);