<?php

namespace all\core;

use zool\aspects\ZElement;

class Fragment extends ZElement{

  public $rendered = true;
  public $id;

  public function init(){
    if($this->rendered){
      $this->preRenderChildren();
    }
  }

  public function render(){
    if($this->rendered){
      return $this->getChildrenOutput();
    }
    return '';
  }

}