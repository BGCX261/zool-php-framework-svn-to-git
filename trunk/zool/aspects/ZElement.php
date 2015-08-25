<?php

namespace zool\aspects;

use zool\context\PageContext;

use zool\ZPropertyException;

use zool\zx\ZMethodExpression;

use Annotation\Annotations;

use zool\xml\elements\ZXmlElement;

use zool\base\ZComponent;

abstract class ZElement extends ZXmlElement{

  const METHOD_PROPERTY = 1;
  const BINDING_PROPERTY = 2;
  const SIMPLE_PROPERTY = 3;

  public function __construct($namespace, $elem, $parent){

    parent::__construct($elem, $parent);

    $class = strtolower(get_class($this));

    $this->meta->name = substr($class, strrpos($class, '\\')+1);
    $this->meta->namespace = $namespace;

    /*
     * Object will has attributes as property
     */
    foreach($this->meta->attributes as $key => $value){

      $annotations = Annotations::ofProperty($this, $key);

      $propertyType = self::SIMPLE_PROPERTY;
      foreach ($annotations as $annotation){
        if(get_class($annotation) == 'Annotation\\Standard\\MethodAnnotation'){
          $propertyType = self::METHOD_PROPERTY;
          break;
        }
        if(get_class($annotation) == 'Annotation\\Standard\\BindingAnnotation'){
          $propertyType = self::BINDING_PROPERTY;
          break;
        }
      }

      switch($propertyType){

        case self::METHOD_PROPERTY:
          $val = $this->evaluateZXAsMethod($value, $this);
          break;

        case self::BINDING_PROPERTY:
          $val = $this->bindValue($value,$this);
          break;

        case self::SIMPLE_PROPERTY:
        default:
          $val = $this->evaluateZX($value, $this);
          break;
      }

      $this->$key = $val;

      unset($val);
    }

    $this->meta->attributes = array();

    $this->init();
  }

  public function __set($property, $value){
    $this->meta->attributes[$property] = $value;
  }

  protected function init(){
    $this->preRenderChildren();
  }

  protected function beforeRender(){}

  protected function bindNotNullPropertyToAttribute($property, $attribute = null){
    if($attribute == null) $attribute = $property;
    if($this->$property != null){
      $this->meta->attributes[$attribute] = $this->$property;
    }
  }

  protected function bindPropertyToAttribute($property, $attribute = null){
    if($attribute == null) $attribute = $property;
    $this->meta->attributes[$attribute] = $this->$property;
  }

  protected function getActionHandlerScript($propertyName, $callBack='', $data = array(), $dataString = ''){

    $methodExpression = $this->$propertyName;
    if( !($methodExpression instanceof ZMethodExpression)){
      throw new ZElementException('Action property must be ZMethodExpression.');
    }

    $methodId = $methodExpression->getId();

    PageContext::instance()->set($methodId, $methodExpression);
    $crp = $methodExpression->getContextRootPath();
    PageContext::instance()->addToRootPaths($crp[0], $crp[1]);


    // TODO urlManager
    $url = 'http://'.$_SERVER['SERVER_NAME'].'/zool/index.php';

    $data['action'] = $methodId;

    $resultHandler = '';
    if(!empty($this->reRender)){

      $viewId = str_replace(APP_PATH, '', $this->document->viewId);

      /*
       * multiple rerender
       */
      if(strpos($this->reRender, ',') !== false){
        $reRenders = array_map('trim', explode(',', $this->reRender));
        foreach ($reRenders as $key => $reRender){

          if(strpos($reRender, ':') === false){
            $reRenders[$key] = $viewId .':'. $reRender;
          }

        }
        $this->reRender = implode(',',$reRenders);

      }elseif(strpos($this->reRender, ':') === false){
        $this->reRender = $viewId .':'. $this->reRender;
      }

     $data['reRender'] = $this->reRender;

    }
    $resultHandler = 'Zool.handleResponse(data);';
    $resultHandler .= $callBack;

    $requestData = str_replace('"', "'", json_encode($data));

    if($dataString != ''){
      $replacement = $dataString . ', ';
      $requestData = substr_replace($requestData, $replacement, 1, 0);
    }

    return <<<SCRIPT
(function(self){Sys.ajax({url:'$url', data: $requestData, parse: 'json', success: function(data){{$resultHandler}}});})(this);
SCRIPT;

  }

  public function render(){
    $this->beforeRender();
    return '';
  }

  // TODO
  private function evaluateZXAsMethod($oldvalue, $context){
    if(($expression = ZMethodExpression::parse($oldvalue, $context)) !== false){
      return $expression;
    }else{
      throw new ZPropertyException("$oldvalue is not an  method expression.");
    }
  }

  // TODO
  private function bindValue($value, $context){
    return $value;
  }


}