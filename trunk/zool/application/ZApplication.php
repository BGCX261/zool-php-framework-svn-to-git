<?php

namespace zool\application;

use zool\context\EventContext;

use zool\ZException;

use zool\context\Contexts;

use zool\zx\ZMethodExpression;

use zool\context\PageContext;

use zool\ZoolBase;

use zool\Zool;

use Annotation\Annotations;

use Doctrine\Common\Annotations\AnnotationRegistry;

use zool\context\SessionContext;

use app\model\Bug;

use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use zool\viewprovider\ZWebViewProvider;
use zool\viewprovider\ZXulViewProvider;
use zool\viewprovider\ZViewProvider;
use zool\base\ZBaseApplication;

/**
 *
 * Enter description here ...
 * @author dev
 *
 */
class ZApplication extends ZBaseApplication{

  private $viewProvider;

  private $entityManager;

  private $request = 'view/index.zool';

  public function __construct($config = array()){
    parent::__construct($config);

    set_error_handler(array($this, 'errorHandler'));
    set_exception_handler(array($this, 'exceptionHandler'));

    $asp = isset($_GET['asp']) ? $_GET['asp'] : 'xul';
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
    $reRender = isset($_REQUEST['reRender']) ? $_REQUEST['reRender'] : null;
    $zoolForm = isset($_REQUEST['zoolForm']) ? $_REQUEST['zoolForm'] : null;

    Annotations::$config['cachePath'] = APP_PATH . '/runtime';

    // Zool specific annotations
    Annotations::getManager()->registry['scope'] = 'Annotation\Standard\ScopeAnnotation';
    Annotations::getManager()->registry['method'] = 'Annotation\Standard\MethodAnnotation';

    // obtaining the entity manager

    $em = $this->entityManager;


    if($asp == 'xul'){
      $this->viewProvider = new ZXulViewProvider();
    }else{
      $this->viewProvider = new ZWebViewProvider();
    }

    /**
     * Input value binding
     */

    if($zoolForm !== null && is_array($zoolForm)){
      foreach ($zoolForm as $key => $value){
        list($var, $scope) = explode(':', $key, 2);
        try{

          if($scope == UNSPECIFIED_SCOPE) $scope = EVENT_SCOPE;

          Contexts::instance()->setToContext($scope, $var, $value);
        }catch (ZException $e){
          throw new ZException($e->getMessage());
        }
      }
    }



      if($action !== null){
        $methodExpression = PageContext::instance()->get($action);

        if($methodExpression instanceof ZMethodExpression)
        $methodExpression->run(); // there is no outputing
      }

      EventContext::instance()->reset();

      if($reRender !== null){
        $this->viewProvider->handleReRender();
      }

      if($action !== null || $reRender !== null){die();}


  }

  public function processRequest()
  {
    if(is_array($this->catchAllRequest) && isset($this->catchAllRequest[0]))
    {
      $route=$this->catchAllRequest[0];
      foreach(array_splice($this->catchAllRequest,1) as $name=>$value)
      $_GET[$name]=$value;
    }
    else
    $route=$this->getUrlManager()->parseUrl($this->getRequest());

    $this->runController($route);

  }

  public function run(){
    $this->viewProvider->render();
  }

  public function getRequest(){
    return $this->request;
  }

  public function getViewProvider(){
    return $this->viewProvider;
  }

  public function getEntityManager(){
    if($this->entityManager == null){

      $config = Setup::createAnnotationMetadataConfiguration(array(APP_PATH.'/model'), ZOOL_DEBUG);

      $conn = array(
      'driver' => 'pdo_mysql',
      'host' =>'localhost',
      'user' => 'root',
      'password' => '',
      'dbname' => 'zool_zool'
      );

      $this->entityManager = EntityManager::create($conn, $config);
    }
    return $this->entityManager;
  }

  /**
   * Alias of getEntityManager();
   */
  public function  getEm(){
    return $this->getEntityManager();
  }


  public function errorHandler($errno, $errstr, $errfile, $errline){
    die("Error: [$errno] $errstr in file: $errfile at line $errline.\n");
  }

  public function exceptionHandler($e){
    die("Exception: [$e->getCode()] $e->getMessage() in file: $e->getFile() at line $e->getLine().\n");
  }

}