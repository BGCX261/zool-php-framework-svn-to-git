<?php

namespace zool\aspects;

use zool\base\ZComponent;

use zool\Zool;
use zool\ZException;
use zool\ILibrary;
use zool\xml\ZXml;

class ZLibrary extends ZComponent implements ILibrary{

  protected $namespace = '';
  protected $outNamespace = '';
  protected $aspect = '';
  protected $path = '';
  protected $library = '';

  public function __construct($namespace, $url, $path, $aspect){

    $parsedUrl = parse_url($url);
    $lib = $parsedUrl['host'];

    $this->aspect = $aspect;

    if(isset($parsedUrl['query'])){
      if(substr($parsedUrl['query'], 0, 3) == 'ns='){
        $oNS = substr($parsedUrl['query'], 3);
        $this->outNamespace = $oNS === false ? '' : $oNS;
      }
    }

    $this->library = $parsedUrl['host'];
    $this->namespace = $namespace;
  }

  public function createElement($name, $elem = array(), $parent = null){

    $ucname = $this->aspect.'\\'.$this->library.'\\' . ucfirst($name);

    try{
      return new $ucname($this->outNamespace, $elem, $parent);
    }catch(ZException $e){
      throw $e;
    }catch(Exception $e){
      return null;
    }

  }

  public function getNamespace(){
    return $this->namespace;
  }

  public function getUrl(){
    return $this->url;
  }

  public function getName(){
     $this->library;
  }

   public function getPath(){
     return $this->path;
   }

}