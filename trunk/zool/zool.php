<?php

namespace zool;


define('APP_PATH', BASE_PATH. '/application');
define('APP_RUNTIME_PATH', APP_PATH.'/runtime');

define('ZOOL_PATH', dirname(__FILE__));
define('ZOOL_BASE_ASPECTS_PATH', ZOOL_PATH.'/aspects');
define('ZOOL_3DPARTY_PATH', ZOOL_PATH.'/3dparty');
define('ZOOL_CONTEXT_NAME', 'contexts');

/*
 * Scopes
 */
define('UNSPECIFIED_SCOPE', 0);
define('EVENT_SCOPE', 1);
define('SESSION_SCOPE', 2);
define('PAGE_SCOPE', 3);
define('REQUEST_SCOPE', 4);
define('APPLICATION_SCOPE', 5);
define('CONVERATION_SCOPE', 6);



session_start();

require_once ZOOL_PATH.'/base/functions.php';

if(boolconst('ZOOL_DEBUG')){
  require_once ZOOL_3DPARTY_PATH.'/kint/Kint.class.php';
}

require_once ZOOL_PATH.'/base/interfaces.php';
require_once ZOOL_PATH.'/ZoolBase.php';

class Zool extends ZoolBase{}

Zool::init();

spl_autoload_register('zool\Zool::loadClass');
