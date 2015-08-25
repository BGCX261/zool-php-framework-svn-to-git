<?php

namespace zool\xml;

class ZXmlUtil{

  public static function getElementsByName($element, $name, $library){

    if(!is_array($element)) return array(); // perhaps CDATA

    $result = array();

    $searchedElement = self::getTagFullName($library, $name);

    if(is_array($element) && $element[ZXml::TAG_NAME_KEY] == $searchedElement){
      $result[] = &$element;
    }

    if(array_key_exists(ZXml::TAG_CHILDREN_KEY, $element)){
      foreach ($element[ZXml::TAG_CHILDREN_KEY] as $child){
        $result = array_merge($result, self::getElementsByName($child, $name, $library));
      }
    }
    return $result;

  }

  public static function getElementById($tree, $id){
    if(!is_array($tree)) return null;

    $attributes = array_key_exists(ZXml::TAG_ATTRIBUTES_KEY, $tree) ? $tree[ZXml::TAG_ATTRIBUTES_KEY] : array();

    if(array_key_exists('id', $attributes) && $attributes['id'] == $id){
      return $tree;
    }

    $element = null;
    $children = array_key_exists(ZXml::TAG_CHILDREN_KEY, $tree) ? $tree[ZXml::TAG_CHILDREN_KEY] : array();

    foreach ($children as $child) {
      if(( $founded = self::getElementById($child, $id)) !== null){
        return $founded;
        break;
      }
    }

    return $element;
  }

  public static function renderArray($xml, $forceOpenTag = false){

    $o = '<'.$xml[0]. (isset($xml[1]) && is_array($xml[1]) ? self::renderAttributes($xml[1]): '');

    if($forceOpenTag || (isset($xml[2]) && is_array($xml[2]) && count($xml[2]>0))){
      $o .= ">\n";

      if(!$forceOpenTag)
        foreach ($xml[2] as $child){
          $o .= self::renderArray($child);
        }

      if(!$forceOpenTag)
        $o .= "\n</".$xml[0].">\n";
    }else{
      $o .= '/>';
    }
    return $o;
  }

  public static function renderAttributes($attrs){
    $out = array();
    foreach ($attrs as $key => $value){
      if($value != null){
        $out[] = $key.'="'.$value.'"';
      }
    }
    return empty($out) ? '' : ' '.implode(' ', $out);
  }

  private static function getTagFullName($library, $name){
    return (empty($library)) ? $name : $library .':'. $name;
  }

}