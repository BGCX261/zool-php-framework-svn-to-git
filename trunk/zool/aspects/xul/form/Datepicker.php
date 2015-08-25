<?php

namespace xul\form;

use xul\form\abstracts\AbstractFormElement;

class Datepicker extends AbstractFormElement{

  public $value;
  public $default;
  public $type;

  public function init(){
    parent::init();

    if($this->default && empty($this->value)){
      $this->value = $this->default;
    }

    $this->bindNotNullPropertyToAttribute('value');
    $this->bindNotNullPropertyToAttribute('type');

  }

  public function render(){
    parent::render();

    return $this->renderHead(true);

  }

  public function getJSValueGetter(){
    return "Zool.byId('$this->id').value";
  }

}