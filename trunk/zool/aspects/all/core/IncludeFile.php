<?php

namespace all\core;

use zool\aspects\ZElement;

use zool\xml\ZXmlParser;

class IncludeFile extends ZElement{

  public $src = '';

  private $document;

  public function init(){
    $file = APP_PATH . $this->src;
    $this->document = $this->getAspect()->createDocument($file);
  }

  public function render(){
    parent::render();
     $o = $this->document->render();
     return $o;
  }

}
