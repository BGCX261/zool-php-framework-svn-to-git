<?php

namespace all\core;

use zool\aspects\ZElementException;

use zool\aspects\ZElement;

class Converter extends ZElement{

  public $pattern;

  public function init()
  {
    //throw new ZElementException($this->parent->value);
    $this->parent->meta->attributes['value'] = $this->parent->value->format($this->pattern);
  }
}