<?php

namespace zool\tools;

/**
 *
 * Enter description here ...
 * @author dev
 *
 */
class ZUniqueIdGenerator{

  private static $id = 0;

  public  static $prefix = "zid";

  public static function next(){
    return self::$prefix . self::$id++;
  }

}