<?php

define('APP_PATH', __DIR__.'/application/');
define('DEBUG', true);
define('NO_LOG_AJAX', false);

$config = require './application/protected/config/config.php';
require './framework/setup.php';

App::run($config);