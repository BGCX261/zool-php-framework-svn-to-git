<?php

namespace zool;

use zool\application\ZApplication;

abstract class ZoolBase{

  protected static $app = null;
  protected static $paths = array();
  protected static $classToPath = array();

  static function init(){
    self::$paths = array(
    'zool' => ZOOL_PATH,
    'base' => ZOOL_PATH .'/base',
    'all'  => ZOOL_BASE_ASPECTS_PATH . '/all',
    'Doctrine' => ZOOL_PATH .'/doctrine/Doctrine',
    'app' => APP_PATH,
    'Annotation'=>ZOOL_PATH .'/annotation/Annotation',
    );

  }

  public static function loadClass($classToLoad){

    if(is_file($classToLoad)){
      require_once $classToLoad;
      return;
    }

    $class = str_replace('\\', '/', $classToLoad);
    $ns = substr($class, 0, strrpos($class, '\\'));

    $root = substr($class, 0, strpos($class, '/'));
    $classPath = str_replace($root.'/', '' , $class);

    if(!array_key_exists($root, self::$paths)){
     // throw new ZException("Unregistred namespace: '$root' at loading $classToLoad.");
     return;
    }

    $fullPath = self::$paths[$root] . "/$classPath.php";

    if(!file_exists($fullPath)){
      throw new ZException("Unable to load class: $class");
    }

    require_once $fullPath;

    self::$classToPath[$classToLoad] = $fullPath;
  }

  public static function import($path, $ns = ''){
    if(!in_array($path, self::$paths)){
      self::$paths[$ns]=$path;
    }
  }

  public static function getRootPath($root){
    return  self::$paths[$root];
  }

  public static function getClassPath($class){
    if(array_key_exists($class, self::$classToPath)){
      return self::$classToPath[$class];
    }else{
      self::loadClass($class);
      return self::$classToPath[$class];
    }
  }

  public static function app(){

    if(self::$app == null){
      throw new ZException('No application instace.');
    }
    return self::$app;
  }

  public static function setApplication($appliaction){
    self::$app = $appliaction;
  }

  // TODO
  public static function createConsoleApplication($config=null)
  {
    return self::createApplication('ZConsoleApplication',$config);
  }

  /**
   * Creates an application of the specified class.
   * @param string $class the application class name
   * @param mixed $config application configuration. This parameter will be passed as the parameter
   * to the constructor of the application class.
   * @return mixed the application instance
   */
  public static function createApplication($config=null, $class = 'zool\application\ZApplication')
  {
    return new $class($config);
  }

  public static function log($str){
    foreach(explode("\n", $str) as $line)
    $log .= date("H:i:s", time()). " " . $line ."\n";

    file_put_contents(BASE_PATH."/log.log", $log, FILE_APPEND);
  }

  public static function debug(){
    if(defined('ZOOL_DEBUG') && ZOOL_DEBUG){
      var_dump(func_get_args());
    }
  }

  public static function trace($str){
    if(defined('ZOOL_TRACE') && ZOOL_TRACE){
      echo 'Zool trace: '. $str . "<br />\n";
    }
  }

  public static function dump($a1){
    ob_start();
    var_dump($a1);
    return ob_get_clean();
  }

  public static function objectHash(&$object){

    if($object instanceof ZComponent){
      return $object->oid;
    }

    if(!isset($object->__oid__)){
      ob_start(); var_dump($object, microtime());
      $stamp = ob_get_clean();
      $object->__oid__ = sha1(spl_object_hash($object) . $stamp);
    }
    return $object->__oid__;
  }

}
