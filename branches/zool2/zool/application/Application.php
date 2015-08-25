<?php

namespace zool\application;

use zool\http\Request;

use html\HtmlViewProvider;

use zool\base\BaseApplication;

use zool\scope\EventScope;

use zool\ZException;

use zool\scope\Scopes;

use zool\zx\ZMethodExpression;

use zool\scope\PageScope;

use zool\ZoolBase;

use zool\Zool;


use Doctrine\Common\Annotations\AnnotationRegistry;

use zool\scope\SessionScope;

use app\model\Bug;

use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use zool\viewprovider\ZWebViewProvider;
use zool\viewprovider\ZXulViewProvider;
use zool\viewprovider\ZViewProvider;


/**
 *
 * @author Zsolt Lengyel
 *
 */
class Application extends BaseApplication{

    private $viewProvider;

    private $entityManager;

    private $request = 'view/index.zool';

    private $aspect;

    private $config;

    public function __construct($config = array()){

        // module name
        parent::__construct('zool', $config);

        $this->config = new Configuration($config);

       // set_error_handler(array($this, 'errorHandler'));
        //set_exception_handler(array($this, 'exceptionHandler'));


        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
        $reRender = isset($_REQUEST['reRender']) ? $_REQUEST['reRender'] : null;
        $zoolForm = isset($_REQUEST['zoolForm']) ? $_REQUEST['zoolForm'] : null;


        $viewAlias = Request::instance()->getViewId();
        $viewAlias = ($viewAlias == null ? 'app.view/index' : $viewAlias) .'.zool';



        $this->viewProvider = new HtmlViewProvider($viewAlias);


        /**
         * Input value binding
         */

        if($zoolForm !== null && is_array($zoolForm)){
            foreach ($zoolForm as $key => $value){
                list($var, $scope) = explode(':', $key, 2);
                try{

                    if($scope == UNSPECIFIED_SCOPE) $scope = EVENT_SCOPE;

                    Scopes::instance()->setToScope($scope, $var, $value);
                }catch (ZException $e){
                    throw new ZException($e->getMessage());
                }
            }
        }



        if($action !== null){
            $methodExpression = PageScope::instance()->get($action);

            if($methodExpression instanceof ZMethodExpression)
                $methodExpression->run(); // there is no outputing
        }

        EventScope::instance()->reset();

        if($reRender !== null){
            $this->viewProvider->handleReRender();
        }

        if($action !== null || $reRender !== null){
            die();
        }


    }

    public function processRequest()
    {

    }

    public function getAspectName(){
        return $this->aspect;
    }

    public function run(){
       header("Content-type: text/html; charset={$this->charset}");

       echo $this->viewProvider->assemble();

    }


    public function getRequest(){
        return Request::instance();
    }

    public function getViewProvider(){
        return $this->viewProvider;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline){
        die("Error: [$errno] $errstr in file: $errfile at line $errline.\n");
    }

    public function exceptionHandler($e){

        echo ("Exception: [{$e->getCode()}] {$e->getMessage()} in file: {$e->getFile()} at line {$e->getLine()}.\n");
       echo "<pre>";
        debug_print_backtrace();
        echo "</pre>";
        die();
    }

}