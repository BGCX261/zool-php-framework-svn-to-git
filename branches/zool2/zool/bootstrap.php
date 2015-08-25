<?php

namespace zool;

if(!defined('BASE_PATH')){
    trigger_error('Please define BASE_PATH.', E_USER_ERROR);
}

define('DS', DIRECTORY_SEPARATOR);
define('APP_MODULE_NAME', 'app');
define('RUNTIME_PATH', APP_PATH.DS.'runtime');
define('RESOURCES_DIRNAME', 'resources');
define('RESOURCES_PATH', BASE_PATH.DS.RESOURCES_DIRNAME);
define('CONFIG_PATH', APP_PATH.DS.'config');
define('ZOOL_SCOPES_PATH', ZOOL_PATH.DS.'scope');
define('ZOOL_ANNOTATION_PATH', ZOOL_PATH.DS.'annotation');
define('ZOOL_VENDOR_PATH', ZOOL_PATH.DS.'vendor');

define('ENABLE_EXCEPTION_HANDLER', true);
define('ENABLE_ERROR_HANDLER', true);

define('ZOOL_CONTEXT_NAME', 'contexts');
define('ANNOTATION_NAMESPACE', 'zool\\annotation\\');


session_start();


require_once ZOOL_PATH.'/base/functions.php';
require_once ZOOL_PATH.'/base/interfaces.php';
require_once ZOOL_PATH.'/deploy/Deployment.php';
require_once ZOOL_PATH.'/ZoolBase.php';
require_once ZOOL_PATH.'/Zool.php';

Zool::init();
