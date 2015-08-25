<?php

namespace zool\http;

use zool\base\Accessable;

/**
 * A HttpCookie instance stores a single cookie, including the cookie name, value, domain, path, expire, and secure.
 *
 * @author Zsolt Lengyel
 */
class HttpCookie extends Accessable
{
    /**
       * @var HttpCookie singleton instance
       */
    private static $instance;

    /**
      * @return HttpCookie instance
      */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new HttpCookie;
        }
        return self::$instance;
    }

    /**
      * Singleton constructor.
      */
    private function __construct(){
    }
}