<?php

namespace xul\control;

class Toolbarbutton extends Button{

  /**
   * @Method
   */
  public $action;

  public function init(){
    parent::init();
    $this->meta->name = 'toolbarbutton';
  }

}