<?php

namespace zool\aspects\web;
use zool\aspects\ZAspect;

class ZWebAspect extends ZAspect{

  public function getLibraryPath(){
    return dirname(__FILE__);
  }

  public function run(){
    $doc = new ZWebDocument($this->baseDocuemt,$this);
    return $doc->render();
  }

  public function getName(){
    return 'web';
  }

}