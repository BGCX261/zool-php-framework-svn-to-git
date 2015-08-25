<?php




use zool\exception\ZoolException;

use zool\event\Events;

use zool\deploy\Deployment;

use zool\vendor\addendum\Annotation;

use zool\vendor\addendum\ReflectionAnnotatedClass;

use zool\util\Strings;

use zool\deploy\ModuleDeployer;

use zool\file\Directory;

!isset($_SERVER['HTTP_USER_AGENT']) || die('Can call from console.');


define('BASE_PATH', realpath(dirname(__FILE__).'/..'));
define('ZOOL_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'zool');
define('APP_PATH', BASE_PATH.DIRECTORY_SEPARATOR.'app');


spl_autoload_register(function($class){
    $class = str_replace('\\', DS, $class);
    if(is_file(BASE_PATH.DS.$class.'.php'))
        require_once BASE_PATH.DS.$class.'.php';
});
// require_once BASE_PATH.'/zool/bootstrap.php';
// require_once BASE_PATH.'/zool/Zool.php';



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


set_error_handler(function($code,$message,$file,$line){
    var_dump( func_get_args());
    echo "\n\n INCOMPLETE DEPLOYMENT \n\n [$code] $message in $file at $line line.";
    die();
});

        class DeployHelper{}


        require_once BASE_PATH.'/zool/scope/ScopeType.php';
        require_once BASE_PATH.'/zool/util/Strings.php';

        require_once BASE_PATH.'/zool/vendor/addendum/Annotation.php';
        require_once BASE_PATH.'/zool/annotation/annotations.php';
        require_once BASE_PATH.'/zool/annotation/annotations.php';
        require_once BASE_PATH.'/zool/deploy/Deployment.php';
        require_once BASE_PATH.'/zool/util/log/LogProvider.php';



        function displayException($exception){

            echo( "\n\n".get_class($exception));
            echo( $exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().")");
            echo( $exception->getTraceAsString());

            if($exception->getPrevious() !== null){
                displayException($exception->getPrevious());
            }
        }

        $appdeployer = new \zool\deploy\ApplicationDeployer([
                'zool'=>ZOOL_PATH,
                'app'=>APP_PATH
                ]);

        $prevmtimehash = $argv[1];

        $app = new Directory(BASE_PATH);
        $files = $app->getFiles(true, ModuleDeployer::PHP_FILE_FILTER);

        $mtime = '';

        foreach ($files as $file){
            if(!Strings::endsWidth($file, Deployment::DEPLOYMENT_DESCRIPTOR)
                    && (Strings::contains($file, 'component')// for app
                            || Strings::contains($file, 'zool')/* for system*/  || true) ){
                $mtime .= filemtime($file);
            }
        }

        $mtimehash = md5($mtime);


        if($prevmtimehash != $mtimehash){

            try{
                $appdeployer->deploy(BASE_PATH);
            }catch (Exception $e){
                echo("\n///////////////////  INCOMPLETE DEPLOYMENT  ///////////////////\n\n");
                displayException($e);
                //echo $mtimehash."\n";
            }

        }

        echo $mtimehash."\n";


