<?php

namespace zool\context;

use zool\ZException;

use Annotation\Standard\ScopeAnnotation;

use Annotation\Annotations;

use zool\base\ZComponent;

class Contexts extends ZComponent{

  private $sessionContext;
  private $pageContext;
  private $requestContext;
  private $eventContext;

  private static $instance = null;

  private $controllerInstances = array();

  private function  __construct(){
    $this->sessionContext = SessionContext::instance();
    $this->pageContext = PageContext::instance();
    $this->requestContext = RequestContext::instance();
    $this->eventContext = EventContext::instance();
  }

  public static function instance(){
    if(is_null(self::$instance)){
      self::$instance = new Contexts();
    }
    return self::$instance;
  }


  public function getSessionContext(){
    return $this->sessionContext;
  }

  public function getPageContext(){
    return $this->pageContext;
  }

  public function getRequestContext(){
    return $this->requestContext;
  }

  public function getEventContext(){
    return $this->eventContext;
  }

  /**
   * Context cannot set, juset get from contexts in the given order.
   * Precedence of contexts :
   * 	- event
   * 	- request
   * 	- page
   * 	- conversation // TODO
   * 	- session
   * 	- application // TODO
   * // TODO more comment about contexts
   */
  public function get($key, $default = null){


    $value = $this->eventContext->get($key);

    if(is_null($value))
      $value = $this->requestContext->get($key);

    if(is_null($value))
      $value = $this->pageContext->get($key);

    if(is_null($value))
      $value = $this->sessionContext->get($key);

    if(!is_null($value)){
      //....
      return $value;
    }

    if($this->isControllerName($key)){
      return $this->instantiateController($key);
    }

    return $default;
  }

  /**
   *
   * Return the constant represents the context.
   * @param $key
   */
  public function getVariableContext($key){

    if(!is_null($this->eventContext->get($key)))
      return EVENT_SCOPE;

    if(!is_null($this->requestContext->get($key)))
      return REQUEST_SCOPE;

    if(!is_null($this->pageContext->get($key)))
      return PAGE_SCOPE;

    if(!is_null($this->sessionContext->get($key)))
      return REQUEST_SCOPE;

      return UNSPECIFIED_SCOPE;
  }

  public function setToContext($context, $key, $value){
    switch($context){
      case EVENT_SCOPE:   $this->eventContext->set($key, $value); break;
      case PAGE_SCOPE:    $this->pageContext->set($key, $value); break;
      case SESSION_SCOPE: $this->sessionContext->set($key, $value); break;
      case REQUEST_SCOPE: $this->requestContext->set($key, $value); break;

      default: throw new ZException('Cannot set variable to context '.$context);
    }
  }

  /**
   * Instantiates the controller, and set to the right scope.
   */
  public function instantiateController($name){

    $class = 'app\\controller\\'.ucfirst($name);

    $controller = new $class;

    $annotations = Annotations::ofClass($controller);

    $scope = EVENT_SCOPE;
    foreach($annotations as $annotation){
      if($annotation instanceof ScopeAnnotation){
        $scope = $annotation->name;
      }
    }

    switch($scope){

      case SESSION_SCOPE :
        $this->sessionContext->set($name, $controller);
        break;

      case PAGE_SCOPE :
        $this->pageContext->set($name, $controller);
        break;
      case REQUEST_SCOPE :
        $this->requestContext->set($name, $controller);
        break;
      case EVENT_SCOPE:
      default:
        // TODO
        break;
        // TODO mode
    }

    return $controller;
  }

  public function isControllerName($name){
    return (substr($name, -10) == 'Controller');
  }

}