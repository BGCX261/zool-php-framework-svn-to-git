<?php

namespace all\core;

use zool\aspects\ZElement;

/**
 *
 * Enter description here ...
 * @author Zsolt Lengyel
 *
 */
class Aspect extends ZElement{

  /**
   * @var string name of aspect
   */
  public $name = '';

  private $rendered = false;

  public function init(){
    /*
     * Aspect tag will work if current aspect is the given.
     */
    $this->rendered = ($this->aspect->name == $this->name);

    if($this->rendered){
      parent::init();
    }
  }

  public function render(){
    if($this->rendered){
      return $this->getChildrenOutput();
    }
    return '';
  }

}