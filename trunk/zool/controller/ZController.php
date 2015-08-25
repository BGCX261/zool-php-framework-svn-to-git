<?php

namespace zool\controller;

use zool\context\RequestContext;

use zool\context\SessionContext;

use Annotation\Standard\OutAnnotation;

use Annotation\Annotations;

use zool\base\ZComponent;

abstract class ZController extends ZComponent{

  private $watchedProperties = array();

  private $requestContext = null;

  public final function __construct(){

    $reflection = new \ReflectionClass($this);
    foreach($reflection->getProperties() as $property ){
      $annotations = Annotations::ofProperty($this, $property->name);
      foreach($annotations as $annotation){

        if ($annotation instanceof OutAnnotation) {
          $this->watchedProperties[$property->name] = $annotation;
        }

      }
    }

  }

  function __set($property, $value){

    $this->$property = $value;

    if(array_key_exists($property, $this->watchedProperties)){
      $annotation = $this->watchedProperties[$property];
      $name = $property;
      if(!is_null($annotation->value)){
        $name = $annotation->value;
      }

      switch ($annotation->scope){

        case SESSION_SCOPE:
          SessionContext::instance()->set($name, $value);
          break;

        default: //TODO

      }
    }
  }

  public function __destruct(){

  }

  public function beforeAction(){}

  public function getRequestContext(){
    if(null == $this->requestContext)
      $this->requestContext = RequestContext::instance();

    return $this->requestContext;
  }

}