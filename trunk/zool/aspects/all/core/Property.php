<?php

namespace all\core;

use zool\aspects\ZElement;

use \zool\Zool;

class Property extends ZElement
{

  public $name = '';
  public $value = '';
  public $append = false;
  public $attribute = false;

  public function init()
  {
    parent::init();
  }

  public function render()
  {
    parent::render();

    $prop = $this->name;


    if ($this->parent instanceof ZElement && !$this->attribute) {
      if ($this->append && is_string($this->parent->$prop))
      $this->parent->$prop .= $this->value;
      else
      $this->parent->$prop = $this->value;

    } else {
      if ($this->append && is_string($this->parent->$prop))
      $this->parent->meta->attributes[$prop] .= $this->value;
      else
      $this->parent->meta->attributes[$prop] = $this->value;
    }
  }

}
