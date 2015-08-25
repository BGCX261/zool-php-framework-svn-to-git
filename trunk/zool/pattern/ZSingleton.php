<?php

namespace zool\pattern;

abstract class ZSingleton{

  private static $instance = null;

  private function  __construct(){}

  public static function instance(){
    if(is_null(self::$instance)){
      self::$instance = new $class;
      self::$instance->init();
    }
    return self::$instance;
  }


}