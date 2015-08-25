<?php

namespace zool\context;

use zool\Zool;

use zool\IContext;

class SessionContext implements IContext{

  const CONTEXT_NAME = 'session';
  const CONTEXT_CLASSES_NAME = 'session_classes';
  const CLASS_NAME_SEPARATOR = ':';

  private $context = array();

  private $classesNeedToLoad = array();

  private static $instance = null;

  private function  __construct(){
    //$this->reset();
    if(!isset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME])){
      $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME] = array();
    }

    if(isset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME])){
      $this->classesNeedToLoad = $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME];
    }

    foreach ($this->classesNeedToLoad as $class){
      Zool::loadClass($class);
    }

    foreach ($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME] as $key => $value){
      $this->context[$key] = unserialize($value);
    }


  }

  /**
   * Saving out the session.
   */
  public function __destruct(){
  foreach ($this->context as $key => $value){
      $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME][$key] = serialize($value);
    }
    $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME] = $this->classesNeedToLoad;

  }

  public static function instance(){
    if(empty(self::$instance)){
      self::$instance = new SessionContext();
    }
    return self::$instance;
  }

  public function set($key, $value){
    if(is_object($value)){
      // TODO recursive class check
      $class = get_class($value);
      $this->addToClasses($class);
    }
    $this->context[$key] = $value;
  }

  public function deset($key){
    unset($this->context[$key]);
    unset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME][$key]);
  }

  public function get($key, $default = null){
    if(isset($this->context[$key])){
      return $this->context[$key];
    }

    return $default;
  }

  public function isSetted($key){
    return isset($this->context[$key]);
  }

  public static function className(){
    return __CLASS__;
  }

  public function addToClasses($class){
    if(!in_array($class, $this->classesNeedToLoad)){
      $this->classesNeedToLoad[] = $class;
    }
  }

  public function reset(){
    $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME] = array();

    $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME] = array();
    $this->context = array();
  }

}