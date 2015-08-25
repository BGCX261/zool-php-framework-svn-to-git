<?php

namespace zool\xml\elements;

use \zool\base\ZComponent;

class ZXmlMeta extends ZComponent{

  public $baseElem = array();
  public $namespace = '';
  public $name = '';
  public $attributes = array();
  public $children = array();
  public $parent = null;
  public $namespaces = array();

}