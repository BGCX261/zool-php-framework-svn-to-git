<?php

namespace zool\base\module;

use zool\base\Accessable;

use Symfony\Component\Yaml\Exception\ExceptionInterface;

use zool\file\File;

use zool\exception\NotSupportedFunction;

use zool\application\Configuration;

use zool\deploy\Deployment;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class ModuleInfo implements \ArrayAccess{

    const MODULE_INFO_FILENAME = 'module-info.php';

    const MODULE_INFO_STUB_FILENAME = 'module-info-stub.php';

    private static $moduleInfos = [];

    private static $moduleInfoStub = null;


    /**
     * @var ModuleInfo module info for module
     */
    public static function forModule($module){

        if(self::$moduleInfoStub == null){
            $stubFile = new File(dirname(__FILE__).'/'.self::MODULE_INFO_STUB_FILENAME);
            self::$moduleInfoStub = $stubFile->includeFile();
        }

        if(!array_key_exists($module, self::$moduleInfos)){

            self::$moduleInfos[$module] = new ModuleInfo($module);

        }

        return self::$moduleInfos[$module];

    }

    private $infos = [];

    /**
     * Default.
     */
    private function __construct($module){
        $this->infos = array_merge_recursive(self::$moduleInfoStub, Deployment::instance()->modules[$module]['info']);
    }

    /**
     *
     */
    public function __get($key){
        if($key == 'infos'){
            return $this->infos;
        }
        return $this->infos[$key];
    }

    public function offsetGet($key){
        return $this->infos[$key];
    }

    public function offsetExists($key){
        return isset($this->infos[$key]);
    }

    public function offsetSet($offset, $value){
        throw new NotSupportedFunction();
    }

    public function offsetUnset($offset){
        throw new NotSupportedFunction();
    }



}
