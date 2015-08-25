<?php

namespace xul\form\abstracts;

use zool\context\Contexts;

use zool\xml\ZXml;

use zool\zx\ZExpression;

use xul\form\Form;

use zool\xml\elements\ZXmlRootElement;

use zool\aspects\ZElement;

use zool\aspects\ZElementException;

use zool\aspects\ZDocument;

abstract class AbstractFormElement extends ZElement{

  public $id;

  private $formParent;

  private $valueBind;
  private $valueBindScope;


  public function __construct($namespace, $elem, $parent){
    if(array_key_exists('value', $elem[ZXml::TAG_ATTRIBUTES_KEY])){
      $this->valueBind = ZExpression::unpackExpression($elem[ZXml::TAG_ATTRIBUTES_KEY]['value']);
    }
    parent::__construct($namespace, $elem, $parent);
  }

  public function init(){
    $this->valueBindScope = Contexts::instance()->getVariableContext($this->valueBind);
    $form = $this->getFormParent();
    $form->registerElement($this);

    $this->bindPropertyToAttribute('id');
    parent::init();
  }

  public function getFormParent(){
    if(is_null($this->formParent)){

      $parent = $this->getParent();
      while( !($parent instanceof Form || $parent instanceof ZXmlRootElement)){
        $parent = $parent->getParent();
      }

      if($parent instanceof Form){
        $this->formParent = $parent;
      }else{
        throw new ZElementException('AbstractFormElement must be in Form tag.');
      }

    }
    return $this->formParent;
  }

  public function getValueBind(){
    return $this->valueBind;
  }

  public function getValueBindScope(){
    return $this->valueBindScope;
  }

  protected abstract function getJSValueGetter();

}