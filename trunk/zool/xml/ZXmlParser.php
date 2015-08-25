<?php

namespace zool\xml;

use zool\ZException;

use zool\tools\ZUniqueIdGenerator;

class ZXmlParser
{

  public static function fromFileToTree($file)
  {

    if(!is_file($file)){
      throw new ZException("Cannot open file $file.");
    }

    $fileName = str_replace(BASE_PATH, '', $file);
    $stat = stat($file);
    $fileNameHash = base64_encode($fileName. '.'.$stat['mtime']);

    $fileRuntimePath = APP_RUNTIME_PATH.'/'.$fileNameHash;

    /*
     * Catching
     */
    if(file_exists($fileRuntimePath)&& !ZOOL_DEBUG){

      $contents = file_get_contents($fileRuntimePath);
      return unserialize($contents);

    }else{

      /*
       *  Remove old file
       */
      foreach(scandir(APP_RUNTIME_PATH) as $runtimeFile){
        $decoded = base64_decode($runtimeFile);

        // this is the old cached file
        if(strpos($decoded, $fileName) === 0){
          unlink(APP_RUNTIME_PATH.'/'.$runtimeFile);
        }
      }

      $doc = self::toTree(file_get_contents($file));
      $serialized = serialize($doc);

      file_put_contents($fileRuntimePath, $serialized);

      return $doc;

    }
  }

  public static function toTree($contents)
  {
    $contents = trim($contents);

    if (!function_exists('xml_parser_create')) {
      // TODO use another parser instead
      return array();
    }
    $parser = xml_parser_create('');

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
    xml_parse_into_struct($parser, $contents, $xml_values, $indexes);
    xml_parser_free($parser);

    $current = &$xml_values[0];
    $nodes = array();

    // if true, must handle another way the result
    $completeRoot = false;

    // for each element will be other
    //$currentId = ZUniqueIdGenerator::next();

    foreach($xml_values as $key => &$elem){

      $elem[ZXml::TAG_NAME_KEY] = $elem['tag'];
      unset($elem['tag']);

      if(isset($elem['attributes'])){
        $elem[ZXml::TAG_ATTRIBUTES_KEY] = $elem['attributes'];
        unset($elem['attributes']);
      }

      // each element must have ID
      if(!isset($elem[ZXml::TAG_ATTRIBUTES_KEY]['id'])){
        // $elem[ZXml::TAG_ATTRIBUTES_KEY]['id'] = ZUniqueIdGenerator::next();
      }

      // No root, no document
      if(!array_key_exists('level', $elem)){
        return array(0=>$contents, 1=>null);
      }

      $level = $elem['level'];
      $type = ZXml::typeToInt($elem['type']);
      unset($elem['type']);

      switch($type){
        case ZXml::OPEN_TYPE:
          $elem[ZXml::TAG_CHILDREN_KEY] = array();

          if(isset($elem['value'])){
            $value = $elem['value'];
            $elem[ZXml::TAG_CHILDREN_KEY] = array(0 => $value);
          }
          unset($elem['value']);

          $nodes[$level-1] = &$elem;
          $current = &$elem;

          break;

        case ZXml::CDATA_TYPE:
          if($value = trim($elem['value']) != ''){
            $elem['tag'] = 'CDATA';
            $current[ZXml::TAG_CHILDREN_KEY][] = &$elem;
          }
          break;

        case ZXml::COMPLETE_TYPE:
          if(isset($elem['value'])){
            $value = $elem['value'];
            $elem[ZXml::TAG_CHILDREN_KEY] = array(0 => $value);
          }
          unset($elem['value']);

          if(empty($nodes)){
            $completeRoot = true;
            $root = &$elem;
          }else{
            $current[ZXml::TAG_CHILDREN_KEY][] = &$elem;
          }

            break;

        case ZXml::CLOSE_TYPE:
          $nodes[$level - 2][ZXml::TAG_CHILDREN_KEY][] = &$current;
          $current = &$nodes[$level - 2];
          unset($nodes[$level-1]);
          break;
      }

      unset($elem['level']);
      unset($elem);

      if($completeRoot)break;
    }

    if(!$completeRoot){
      $root = $current[ZXml::TAG_CHILDREN_KEY][0];
    }

    $header = substr($contents, 0, strpos($contents, '<'.$root[0]));

    return array(0=>$header, 1=>$root);

  }


}
