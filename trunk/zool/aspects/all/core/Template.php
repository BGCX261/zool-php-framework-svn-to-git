<?php

namespace all\core;

use zool\aspects\ZElement;

class Template extends ZElement{

  public function init(){
    parent::preRenderChildren();
  }

  public function render(){
    parent::render();
    return $this->getChildrenOutput();
  }
}