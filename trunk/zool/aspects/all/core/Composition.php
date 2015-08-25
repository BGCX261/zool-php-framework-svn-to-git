<?php

namespace all\core;

use zool\aspects\ZElementException;

use zool\xml\ZXml;

use zool\xml\ZXmlUtil;

use zool\aspects\ZElement;

class Composition extends ZElement{

  public $template;

  private $templateDocument;
  private $defines = array();

  public function init(){

    if($this->template == null){
      throw new ZElementException('Composition must have template');
    }

    $this->templateDocument = $this->getAspect()->createTemplateDocument(APP_PATH.$this->template);

    $coreNamespace = $this->getAspect()->getLibraryNamespace('core');
    $mySelf = array(
      ZXml::TAG_NAME_KEY => $this->fullName,
      ZXml::TAG_CHILDREN_KEY => $this->children
    );

    $defineElements = ZXmlUtil::getElementsByName($mySelf, 'define', $coreNamespace);
    $this->defines = $defineElements;

    $params = ZXmlUtil::getElementsByName($mySelf, 'param', $coreNamespace);

    foreach ($params as $param) {

      if(!isset($param[ZXml::TAG_ATTRIBUTES_KEY]['name']) || !isset($param[ZXml::TAG_ATTRIBUTES_KEY]['name'])){
        throw new ZElementException('Param element must have "name" and "value" attributes.');
      }

      $key = $param[ZXml::TAG_ATTRIBUTES_KEY]['name'];
      $value = $param[ZXml::TAG_ATTRIBUTES_KEY]['value'];

      $value= $this->evaluateZX($value);

      $this->templateDocument->setContext($key, $value);
    }

  }

  public function render(){
    $o = parent::render();
    $o .= $this->templateDocument->render($this->document->viewId, $this->defines);
    return $o;
  }
}