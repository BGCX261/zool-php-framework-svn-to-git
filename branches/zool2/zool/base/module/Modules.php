<?php

namespace zool\base\module;

use zool\deploy\Deployment;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Modules{

    /**
       * @var Modules singleton instance
       */
    private static $instance;

    /**
      * @return Modules instance
      */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new Modules;
        }
        return self::$instance;
    }

    public static function get($module){

    }

    /**
      * Singleton constructor.
      */
    private function __construct(){
    }

    /**
     * @param string module name
     * @return string path of module
     */
    public function pathOf($module){

        if(!$this->exists($module)){
            throw new ModuleNotFoundException($module);
        }

        return Deployment::instance()->modules[$module]['path'];

    }

    public function exists($module){
        return isset(Deployment::instance()->modules[$module]);
    }

}
