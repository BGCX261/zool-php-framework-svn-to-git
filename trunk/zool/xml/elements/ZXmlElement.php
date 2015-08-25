<?php

namespace zool\xml\elements;

use zool\aspects\ZDocument;

use zool\ZException;

use zool\Zool;
use zool\xml\ZXml;
use zool\base\ZComponent;
use zool\zx\ZExpression;

class ZXmlElement extends ZComponent{

  protected $meta = null;

  protected $renderedChildren = array();

  protected $context = array();


  public function __construct($elem, $parent)
  {

    $this->meta = new ZXmlMeta;

    $parsedName = $this->parseName($elem[ZXml::TAG_NAME_KEY]);

    $this->meta->baseElem = $elem;
    $this->meta->namespace = $parsedName[0];
    $this->meta->name = $parsedName[1];
    $this->meta->parent = $parent;

    if(isset($elem[ZXml::TAG_ATTRIBUTES_KEY]))
    $this->meta->attributes = $elem[ZXml::TAG_ATTRIBUTES_KEY];

    if(isset($elem[ZXml::TAG_CHILDREN_KEY]))
    {$this->meta->children = $elem[ZXml::TAG_CHILDREN_KEY] ;}

    if (is_array($this->meta->attributes)){
      foreach ($this->meta->attributes as $key => $value) {
        if (substr($key, 0, 5) == 'xmlns') {
          $namespace = '';
          if (substr($key, 5, 1) == ':') {
            $ns = explode(':', $key, 2);
            $namespace = $ns[1];
          }
          $this->meta->namespaces[$namespace] = $value;

          $this->parseNamespace($namespace, $value);

          unset($this->meta->attributes[$key]);
        }
      }
    }

  }

  public function getMeta()
  {
    return $this->meta;
  }

  public function getDocument()
  {
    $parent = $this->meta->parent;
    while(!($parent instanceof ZDocument)){
      $parent = $parent->meta->parent;
    }
    return $parent;
  }

  public function getParent(){
    return $this->meta->parent;
  }

  public function getChildren(){
    return $this->meta->children;
  }


  protected function parseName($name)
  {

    if ($pos = strpos($name, ':')) {
      $tmp = explode(':', $name);
      return array($tmp[0], $tmp[1]);
    }

    return array('', $name);

  }

  protected function parseNamespace($namespace, $url)
  {
    $parsed = parse_url($url);

    if ($parsed['scheme'] == 'lib') {
      Zool::app()->viewProvider->aspect->registerLibrary($namespace, $url);
      unset($this->meta->namespaces[$namespace]);
    }
  }

  public function setContext($name, $value)
  {
    $this->context[$name] = $value;
  }

  public function getContext($name, $default = null)
  {

    if (array_key_exists($name, $this->context)) {
      return $this->context[$name];
    }
    return $default;
  }

  public function unsetContext($name){
    unset($this->context[$name]);
  }

  public function resolveFromContext($name, $default = null)
  {

    $val = $this->getContext($name);
    if (is_null($val)) {
      $val = $this->getParent()->resolveFromContext($name, $default);
    }
    return (is_null($val) ? $default : $val);
  }

  public function render()
  {

    $o = '';
    if ($lib = $this->aspect->getLibrary($this->meta->namespace)) {

      $elem = $lib->createElement($this->meta->name, $this->meta->baseElem, $this->meta->parent);

      // handling new element
      if(!empty($elem)){

        // renders the library tag
        $o = $elem->render();

        // setting the parent new children
        if (!$this->isRootElement()) {

        }

      }else{
        $this->preRenderChildren();
        $o = $this->renderSimple();
      }

    } else {
      $this->preRenderChildren();
      $o = $this->renderSimple();
    }


    return $o;

  }

  protected function renderHead($complex = false)
  {

    $attributes = $this->renderAttributes();

    $o = '<' . $this->getFullName() . (!empty($attributes) ? ' ' . $attributes : '');
    if (count($this->meta->children) > 0 && !$complex) {
      $o .= ">";
    } else {
      $o .= " />\n";
    }

    return $o;
  }

  protected function getHead(){
    $attributes = $this->renderAttributes();
    return '<' . $this->getFullName() . (!empty($attributes) ? ' ' . $attributes : ''). '>';
  }

  protected function tagPrefix()
  {
    return ($this->meta->namespace == '' ? '' : $this->meta->namespace . ':');
  }

  protected function renderFoot()
  {
    if (count($this->meta->children) > 0) {
      return $this->getFoot();
    }
    return '';
  }

  protected function getFoot(){
    return '</' . $this->getFullName() . ">\n";
  }

  protected function renderedChildren()
  {
    if (is_array($this->meta->children) && !empty($this->meta->children) && count($this->renderedChildren) != count($this->meta->children)) {
      throw new ZException('Children have not prerendered yet for '.$this->meta->name);
    }
    return implode('', $this->renderedChildren);
  }

  protected function renderSimple()
  {

    //  $this->preRenderChildren();

    $o = $this->renderHead();
    $o .= $this->renderedChildren();
    $o .= $this->renderFoot();

    return $o;
  }

  protected function preRenderChildren()
  {
    // must reset before filling
    $this->renderedChildren = array();
    if(is_array($this->children))
    foreach ($this->children as $child) {
      if(is_string($child)){
        $this->renderedChildren[] = $this->evaluateZX($child);
      }else{
        $childElem = new ZXmlElement($child, $this);
        $rendered = $childElem->render();
        $this->renderedChildren[] = $rendered;

      }

    }

  }

  protected function getChildrenOutput(){
    return implode('', $this->renderedChildren);
  }

  protected function renderAttributes()
  {
    $o = array();

    foreach ($this->meta->namespaces as $key => $value) {
      $o[] = "xmlns" . ($key == '' ? '' : ':') . "$key=\"$value\"";
    }

    if (!empty($this->meta->attributes) && is_array($this->meta->attributes)){
      foreach ($this->meta->attributes as $key => $value) {

        $value = $this->evaluateZX($value);

        $o[] = "$key=\"$value\"";
      }
    }
    return join(" ", $o);
  }

  public function getFullName()
  {
    return $this->tagPrefix() . $this->meta->name;
  }

  protected function evaluateZX($oldvalue)
  {
    $value = $oldvalue;
    if (($expr = ZExpression::parse($value, $this)) !== false) {
      try {
        $value = $expr->getValue();

        // its just a nested ZX
        if($oldvalue != $expr->fullExpression){
          $value = str_replace($expr->fullExpression, $value, $oldvalue);
        }
      }
      catch (ZExpressionException $e) {
        throw new ZException("Failed to evaluate ZX in $this->fullName. ZX: {$expr->expression}",
        201, $e);
      }
    }

    return $value;
  }

  public function isRootElement()
  {
    return $this instanceof ZXmlRootElement;
  }

  public function getAspect(){
    return $this->viewProvider->aspect;
  }

  public function getViewProvider(){
    return Zool::app()->viewProvider;
  }


  public function __toString(){
    return $this->fullName;
  }

}