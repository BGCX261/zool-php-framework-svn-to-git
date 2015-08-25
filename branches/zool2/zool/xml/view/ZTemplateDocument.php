<?php

namespace zool\aspects;

use zool\ZException;

use zool\xml\ZXml;

use zool\xml\ZXmlUtil;

class ZTemplateDocument extends ZDocument{

  const INSERT_TAGNAME = 'insert';
  const INSERT_NAME_ATTRIBUTE = 'name';
  const CORE_LIBRARY = 'core';

  private $coreLibrary = 'z';
  private $insertTagname = '';

  public function init(){
  }

  public function render($viewId = null, $defines = array()){

    if($viewId === null){
      throw new ZException('ViewId must defined at call of '.get_class($this).'#render().');
    }

    /*
     * Binding viewId to the composition's
     */
    $this->viewId = $viewId;
    $this->coreLibrary = $this->getAspect()->getLibraryNamespace(self::CORE_LIBRARY);
    $this->insertTagname = empty($this->coreLibrary) ? self::INSERT_TAGNAME: $this->coreLibrary . ':' . self::INSERT_TAGNAME;

    $inserts = ZXmlUtil::getElementsByName($this->getDocumentTree(), self::INSERT_TAGNAME, $this->coreLibrary);

    $inserts = $this->getNameAttributeBinded($inserts);
    $defines = $this->getNameAttributeBinded($defines);

    foreach ($inserts as &$insert){
      if(!array_key_exists(ZXml::TAG_ATTRIBUTES_KEY, $insert) || !array_key_exists(self::INSERT_NAME_ATTRIBUTE, $insert[ZXml::TAG_ATTRIBUTES_KEY])){
        throw new ZElementException('Insert element must have "' . self::INSERT_NAME_ATTRIBUTE.'" attribute.');
      }

      $name = $insert[ZXml::TAG_ATTRIBUTES_KEY][self::INSERT_NAME_ATTRIBUTE];

      /*
       * Replace children if matches any define element.
       */
      if(array_key_exists($name, $defines) && array_key_exists(ZXml::TAG_CHILDREN_KEY, $defines[$name])){
        $insert[ZXml::TAG_CHILDREN_KEY] = $defines[$name][ZXml::TAG_CHILDREN_KEY];
      }
    }

    /*
     * Binding own meta insert.
     */
    $this->bindInserts($inserts, $this->document[1]);

    return parent::render();

  }

  private function getNameAttributeBinded($elems){
    $result = array();
    foreach ($elems as $elem){
      $name = $elem[ZXml::TAG_ATTRIBUTES_KEY][self::INSERT_NAME_ATTRIBUTE];
      $result[$name] = $elem;
    }

    return $result;
  }

  private function bindInserts($inserts, &$elem){

    if(!is_array($elem)) return;
    /*
     * Binding children
     */
    if(array_key_exists(ZXml::TAG_NAME_KEY, $elem) && $elem[ZXml::TAG_NAME_KEY] == $this->insertTagname && array_key_exists($name = $elem[ZXml::TAG_ATTRIBUTES_KEY][self::INSERT_NAME_ATTRIBUTE], $inserts)){ // the elem is insert elem and the name is defined

      $elem[ZXml::TAG_CHILDREN_KEY] = $inserts[$name][ZXml::TAG_CHILDREN_KEY];

    }else{
      /*
       * Search forth
       */
      foreach ($elem as &$child){
        $this->bindInserts($inserts, $child);
      }
    }
  }

}