<?php


namespace Annotation\Standard;

use Annotation\Annotation;
/**
 * Defines a magic/virtual method
 *
 * @usage('class'=>true, 'property'=>true)
 */
class ScopeAnnotation extends Annotation{

  public $name;

  /**
   * Initialize the annotation.
   */
  public function initAnnotation($properties)
  {
    if(empty($properties)){
      $this->name = EVENT_SCOPE;
    }else $this->name = $properties[0];
  }

}
