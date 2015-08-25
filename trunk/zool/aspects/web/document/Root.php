<?php

namespace web\document;

use zool\aspects\ZElementException;

use zool\aspects\ZElement;

class Root extends ZElement{
    
   public $title = '';

  protected function init(){
    // TODO
    if(!$this->isRootElement() && false){
      throw new ZElementException('Root tag must be realy root element.');
    }

    parent::init();
    $this->meta->name = 'html';
  }

  public function render(){
    parent::render();
    $o = '<'.$this->fullName.'>';
    $o .= '<head><title>'.$this->title.'</title><head>';

    $o .= $this->getChildrenOutput();

    $o .= '</'.$this->fullName.'>';

    return $o;

  }

}