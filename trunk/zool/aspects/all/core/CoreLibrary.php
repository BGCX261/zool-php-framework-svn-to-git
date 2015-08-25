<?php

namespace all\core;

use zool\aspects\ZLibrary;

class CoreLibrary extends ZLibrary{

  /**
   * This is a special library. It creates on his way elements.
   * @see zool\aspects.ZLibrary::createElement()
   */
  public function createElement($name, $elem = array(), $parent = null){

    $ucname = 'all\\'.$this->getName().'\\' . ucfirst($name);

    try{
      return new $ucname($this->outNamespace, $elem, $parent);
    }catch(ZException $e){
      // TODO log error
      return null;
    }

  }

  public function getName(){
    return 'core';
  }

  public function getPath(){
    return dirname(__FILE__);
  }

}