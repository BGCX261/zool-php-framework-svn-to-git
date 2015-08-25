<?php

namespace zool\context;

use zool\IContext;

class RequestContext implements IContext{

  const CONTEXT_NAME = 'request';

  private $context = array();

  private static $instance = null;

  private function  __construct(){
    // Unlike the Session and PageContext the RequestContext does not initializes itself.
    $this->set('SERVER', $_SERVER);
  }

  /**
   * Saving out the session.
   */
  public function __destruct(){
    // No Saving
  }

  public static function instance(){
    if(empty(self::$instance)){
      self::$instance = new RequestContext();
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