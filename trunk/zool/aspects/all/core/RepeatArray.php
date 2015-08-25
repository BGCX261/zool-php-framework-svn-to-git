<?php

namespace all\core;

use zool\aspects\ZElementException;

use zool\aspects\ZElement;

class RepeatArray extends ZElement{

  public $value = array();
  public $var = "";

  protected function init(){
   if(empty($this->var)){
      throw new ZElementException("Attribute 'var' must setted.");
    }
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