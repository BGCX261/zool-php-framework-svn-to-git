<?php

use zool\xml\ZXmlParser;
use zool\Zool;

define('ZOOL_DEBUG',true);
define('ZOOL_TRACE', false);

define('BASE_PATH', dirname(__FILE__));

require_once 'zool/zool.php';

$config = include(APP_PATH . '/config/main.php');

$app = Zool::createApplication($config);

$app->run();
