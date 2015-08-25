<?php

namespace Annotation\Standard;

use Annotation\Annotation;
/**
 * Defines a magic/virtual method
 *
 * @usage('property'=>true)
 */
class OutAnnotation extends Annotation{

  public $scope;

  public $value;

  /**
   * Initialize the annotation.
   */
  public function initAnnotation($properties)
  {
    if(isset($properties['scope'])){
      $this->scope = $properties['scope'];
    }else{
      $this->scope = EVENT_SCOPE;
    }

    if(isset($properties['value'])){
      $this->value = $properties['value'];
    }else{
      $this->value = null;
    }

  }

}
