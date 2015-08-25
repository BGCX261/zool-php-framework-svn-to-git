<?php

namespace zool\aspects;

use zool\Zool;
use zool\aspects\web\ZWebDocument;

use zool\base\ZComponent;
use zool\xml\ZXmlParser;

/**
 * This tag drives his children rendering.
 * If the application aspect equals to the given by $name, the children will be rendered.
 * Else won't render anything.
 *
 * @author Zsolt Lengyel
 *
 */
abstract class ZAspect extends ZComponent{

  protected $baseDocuemt;

  protected $libraries = array();

  protected $currentViewId;

  public function __construct($request){

    $aspectPath = $this->getLibraryPath();
    $rootAlias = $this->getName();

    Zool::import($aspectPath, $rootAlias);

    $doc  = ZXmlParser::fromFileToTree(APP_PATH.'/'.$request);
    $this->baseDocuemt = $doc;
    $this->currentViewId = APP_PATH .'/'. $request;
    $this->init();
  }

  public function init(){}

  public function registerLibrary($namespace, $url){
    $parsedUrl = parse_url($url);
    $libName = $parsedUrl['host'];
    $path = $this->getLibraryPath();
    $name = $this->getName();

    // core library is
    // TODO minden hasonlóra így viselkedjen
    if(strtolower($libName == 'core')){
      $name = 'all';
      $path .= '/../all';
    }

    return ($this->libraries[$namespace] = new ZLibrary($namespace, $url, $path, $name));
  }

  public function getLibrary($namespace){
    if(array_key_exists($namespace, $this->libraries)){
      return $this->libraries[$namespace];
    }
    return false;
  }

  public function getLibraryNamespace($library){
    foreach ($this->libraries as $ns => $lib) {

      if($lib->library == $library){
        return $ns;
      }
    }

    return null;

  }

  public function createTemplateDocument($file){
    $doc  = ZXmlParser::fromFileToTree($file);
    return new ZTemplateDocument($file, $doc, $this);
  }

  public abstract function createDocument($file);

  public abstract function run();

  public abstract function getName();

  public abstract function getLibraryPath();

}