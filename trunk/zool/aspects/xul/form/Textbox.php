<?php

namespace xul\form;

use xul\form\abstracts\AbstractFormElement;

use zool\context\Contexts;

use zool\zx\ZExpression;

use zool\xml\ZXml;

use zool\aspects\ZElement;
class Textbox extends AbstractFormElement{

  public $value;
  public $type;
  public $emptytext;
  public $maxlenght;
  public $disabled;
  public $label;
  public $accesskey;
  public $default;


  public function init(){
    parent::init();

    $this->meta->name = 'textbox';

    if($this->default && empty($this->value)){
      $this->value = $this->default;
    }

    $this->bindPropertyToAttribute('value');

    $this->bindNotNullPropertyToAttribute('type');
    $this->bindNotNullPropertyToAttribute('disabled');
    $this->bindNotNullPropertyToAttribute('maxlenght');
    $this->bindNotNullPropertyToAttribute('emptytext');
    $this->bindNotNullPropertyToAttribute('accesskey');

  }

  public function render(){
    parent::render();
    return $this->renderHead();
  }

  public function getJSValueGetter(){
    return "Zool.byId('$this->id').value";
  }

}