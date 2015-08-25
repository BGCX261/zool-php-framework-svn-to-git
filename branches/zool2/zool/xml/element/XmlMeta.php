<?php

namespace zool\xml\element;

use zool\base\Accessable;

use \zool\base\ZComponent;

class XmlMeta extends Accessable{

  public $baseElem = array();
  public $namespace = '';
  public $name = '';
  public $attributes = array();
  public $children = array();
  public $parent = null;
  public $namespaces = array();

}