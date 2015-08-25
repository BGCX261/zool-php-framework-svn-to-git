<?php

namespace zool\viewprovider;

use zool\aspects\web\ZWebAspect;

use zool\aspects\web\ZWebDocument;
use zool\xml\ZXmlParser;
use zool\Zool;

class ZWebViewProvider extends ZViewProvider{


  public function render(){
    parent::render();

    $request = Zool::app()->request;

    $this->aspect= new ZWebAspect($request);
    $view = $this->aspect->run();

    echo $view;

  }


}