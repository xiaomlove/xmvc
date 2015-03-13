<?php

define('APP_PATH', __DIR__.'/application/');
define('DEBUG', false);
define('NO_LOG_AJAX', true);

$config = require './application/protected/config/config.php';
require './framework/setup.php';

App::run($config);