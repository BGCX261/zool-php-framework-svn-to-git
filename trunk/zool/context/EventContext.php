<?php

namespace zool\context;

use zool\IContext;

/**
 *
 * EventContext lives while remote method call is running.
 *
 */
class EventContext implements IContext{

  private $context = array();

  private static $instance = null;

  private function  __construct(){
  }

  /**
   * Saving out the session.
   */
  public function __destruct(){
    // No Saving
  }

  public static function instance(){
    if(empty(self::$instance)){
      self::$instance = new EventContext();
    }
    return self::$instance;
  }

  public function set($key, $value){
    $this->context[$key] = $value;
  }

  public function deset($key){
    unset($this->context[$key]);
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

  public function reset(){
    $this->context = array();
  }

}