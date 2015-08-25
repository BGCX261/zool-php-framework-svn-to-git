<?php

namespace  zool\viewprovider;

use zool\base\ZComponent;

/**
 *
 * Enter description here ...
 * @author Zsolt Lengyel
 *
 */
abstract class ZViewProvider extends ZComponent{

  protected $aspect;

  public function __construct(){
    $this->init();
  }

  abstract function init();

  public function render(){

  }

  public function getAspect(){
    return $this->aspect;
  }

  abstract function handleReRender();

}