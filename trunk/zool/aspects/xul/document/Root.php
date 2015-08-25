<?php

namespace xul\document;

use zool\aspects\ZElementException;

use zool\aspects\ZElement;

class Root extends ZElement{

  protected function init(){
    // TODO
    if(!$this->isRootElement() && false){
      throw new ZElementException('Root tag must be realy root element.');
    }

    parent::init();
    header('Content-type: application/vnd.mozilla.xul+xml; charset: UTF-8');

    $this->meta->name = 'window';
  }

  public function render(){
    parent::render();
    $o = '<?xml version="1.0"?>'.
	'<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>'.
	'<'.$this->fullName.' id="rootWnd" title="Register Online!" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">';

    $o .= $this->getChildrenOutput();

    $o .= '</'.$this->fullName.'>';

    return $o;

  }

}