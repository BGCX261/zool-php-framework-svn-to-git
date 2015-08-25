<?php

namespace all\core;

use zool\aspects\ZElement;

class Insert extends ZElement{

  public $name;

  public function init(){
    parent::preRenderChildren();
  }

  public function render(){
    parent::render();
    return $this->getChildrenOutput();
  }

}