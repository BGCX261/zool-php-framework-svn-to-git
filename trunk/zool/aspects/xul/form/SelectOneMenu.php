<?php

namespace xul\form;

use xul\form\abstracts\AbstractFormElement;

class SelectOneMenu extends AbstractFormElement{

  public $value;

  public function init(){
    parent::init();
    $this->meta->name = 'menulist';
  }

  public function render(){
    parent::render();

    $o = $this->renderHead();
    $o .= '<menupopup>';
    $o .= $this->getChildrenOutput();
    $o .= '</menupopup>'.$this->renderFoot();

    return $o;

  }

  public function getJSValueGetter(){
    return "Zool.byId('$this->id').selectedItem.value";
  }

}