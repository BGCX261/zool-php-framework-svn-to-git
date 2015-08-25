<?php

namespace xul\form;

use zool\aspects\ZElement;

class Menuitems extends ZElement{

  public $value;
  public $var;
  public $itemLabel;
  public $itemValue;

  public function init(){
    parent::init();
    $this->meta->children[] = array(0=>'menuitem', 1=>array(
      'value'=>'#{'.$this->itemValue.'}',
      'label'=>'#{'.$this->itemLabel.'}'
    ));
  }

  public function render(){
    parent::render();
    if(!is_array($this->value)) return;

    ob_start();
    foreach ($this->value as $v) {
      $this->setContext($this->var, $v);
      $this->preRenderChildren();
      echo implode('', $this->renderedChildren);
    }
    return ob_get_clean();

  }

}