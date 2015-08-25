<?php

namespace zool\aspects;

use zool\base\ZComponent;

use zool\context\Contexts;

use zool\xml\elements\ZXmlRootElement;

abstract class ZDocument extends ZComponent{

  protected $document = array();

  protected $header = '';

  protected $documentContext = array();

  protected $contexts;

  protected $context = array();

  private $aspect;

  protected $viewId;

  public function __construct($viewId ,$doc, $aspect){

    $this->viewId = $viewId;
    $this->aspect = $aspect;

    $this->document = $doc;
    $this->header = $this->document[0];

    if(empty($this->document)){
      return;
    }

    $this->contexts = Contexts::instance();

    $this->init();
  }

  public function init(){}

  public function render(){
    // o like output

    $rootELement = $this->createRootElement();

    $o = $this->header;

     if(!is_null($rootELement))
      $o .= $rootELement->render();

    return $o;
  }

  public function getAspect(){
    return $this->aspect;
  }

  public function getViewId(){
   return $this->viewId;
  }

  protected function createRootElement(){
    $tree = $this->getDocumentTree();
    if(!empty($tree))
      return new ZXmlRootElement($this->getDocumentTree(), $this);
    else return null;
  }

  public function getDocumentTree(){
    return $this->document[1];
  }

  public function setContext($key, $value){
    $this->context[$key] = $value;
  }

  public function getContext($key, $default = null){
    if(array_key_exists($key, $this->context)){
      return $this->context[$key];
    }
    return $default;
  }

  public function resolveFromContext($name, $default = null)
  {
    $value = $this->getContext($name);
    if(is_null($value)){
      $value = Contexts::instance()->get($name, $default);
    }
    return (is_null($value) ? $default : $value);
  }

}