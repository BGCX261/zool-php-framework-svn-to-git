<?php

namespace basetag\element\core;

use zool\xml\view\AbstractElement;

use zool\aspects\ZElement;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Repeat extends AbstractElement{

  public $value = array();
  public $var = "";

  protected function init(){
      if(empty($this->var)){
          throw new ZElementException("Attribute 'var' must setted.");
      }
  }

  public function assemble(){
      parent::assemble();
      if(!is_array($this->value)) return;

      $children = [];

      ob_start();
      foreach ($this->value as $v) {
          $this->setContext($this->var, $v);
          $this->preAssembleChildren();
          $children[] = $this->getPreAssembledChildren();
      }

      return $this->compose(null, null, $children);
  }

}