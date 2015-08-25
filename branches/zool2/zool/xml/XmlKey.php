<?php

namespace zool\xml;

/**
 * Type of XML entites.
 *
 * @author Zsolt Lengyel
 *
 */
class XmlKey{

  const OPEN_TYPE = 0;
  const CLOSE_TYPE= 1;
  const COMPLETE_TYPE = 2;
  const CDATA_TYPE = 3;

  const TAG_NAME_KEY = 0;
  const TAG_ATTRIBUTES_KEY = 1;
  const TAG_CHILDREN_KEY = 2;


  public static function typeToInt($type){
    if($type == 'open')
    return self::OPEN_TYPE;
    if($type == 'close')
    return self::CLOSE_TYPE;
    if($type == 'cdata')
    return self::CDATA_TYPE;
    if($type == 'complete')
    return self::COMPLETE_TYPE;
  }
}