<?php

namespace zool\base\module;

use zool\base\Accessable;

use zool\application\Configuration;

use zool\application\ModuleNotFoundException;

use zool\application\Modules;

use zool\Zool;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Module extends Accessable{

    private static $modules = [];



    /**
     *
     * @param string $module name of module
     * @return Module module
     */
    public static function forName($module){

        if(!isset(self::$modules[$module])){

            if(!Modules::instance()->exists($module)){
                throw new ModuleNotFoundException($module);
            }

            self::$modules[$module] = new Module($module);
        }

        return self::$modules[$module];

    }

    /**
     * @var Configuration
     */
    protected $configuration;

    private $name;

    /**
     *
     */
    protected function __construct($name, $config = null){

        $this->name = $name;
        $this->configuration = $config == null ? Zool::app()->getConfiguration()->get($name) : new Configuration($config);

        if(!isset(self::$modules[$name])){
            self::$modules[$name] = $this;
        }
    }

    public function getConfiguration(){
        return $this->configuration;
    }

    protected function setConfiguration($config){
        $this->configuration = $config;
    }

    public function getName(){
        return $this->name;
    }


    public function getInfo(){
        return ModuleInfo::forModule($this->name);
    }


}
