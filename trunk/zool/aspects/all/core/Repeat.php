<?php

namespace all\core;

use zool\aspects\ZElement;

class Repeat extends ZElement{

  public $times = 2;


  public function init(){

  }

  public function render(){
    parent::render();
    ob_start();
    for($i=0; $i < $this->times; ++$i){
      $this->renderedChildren = array();
      $this->preRenderChildren();
      echo implode('', $this->renderedChildren);
    }
    return ob_get_clean();

  }

}