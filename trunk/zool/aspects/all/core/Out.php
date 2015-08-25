<?php

namespace all\core;

use zool\aspects\ZElement;
class Out extends ZElement{

  public $value="";

  public function render(){
    return $this->value;
  }

}