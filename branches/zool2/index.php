<?php

use zool\Zool;

define( 'DEBUG', true );

define('BASE_PATH', dirname(__FILE__));
define('ZOOL_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'zool');
define('APP_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'app');

require ZOOL_PATH.'/vendor/kint/Kint.class.php';


require_once dirname(__FILE__).'/zool/bootstrap.php';
$config = require_once CONFIG_PATH.DS.'development.php';

$app = Zool::createApplication($config);
$app->run();


