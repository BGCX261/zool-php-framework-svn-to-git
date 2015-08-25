<?php

namespace all\core;

use zool\aspects\ZElement;
use zool\annotation\Action;

class Dump extends ZElement{


  public $var = null;
  /**
   * @Method
   */
  public $action;

  public function render(){
    ob_start();
    echo "<pre>";
    var_dump($this->var);
    echo "</pre>";
    return ob_get_clean();
  }

}