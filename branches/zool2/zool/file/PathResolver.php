<?php

namespace zool\file;


use zool\base\module\ModuleNotFoundException;

use zool\base\module\Modules;

use zool\base\module\ModuleException;

use zool\util\Strings;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class PathResolver{

    /**
     * @var PathResolver singleton instance
     */
    private static $instance;

    /**
     * @return PathResolver instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new PathResolver;
        }
        return self::$instance;
    }

    /**
     * Singleton constructor.
     */
    private function __construct(){
    }

    const MODULE_SEPARATOR = '.';
    const PATH_SEPARATOR = '/';

    /**
     * @param $alias cannot start with path separator. The module file alias looks like: module.path/file.php.<br/>
     * So the pattern is like: {module name}.{path in module}
     */
    public function resolve($alias){

        list($moduleName, $path) = $this->moduleNameAndPath($alias);
        $modulePath = Modules::instance()->pathOf($moduleName);

        $resourceFile = new File($modulePath .'/'. $path);


        if(!$resourceFile->exists()){
            throw new FileNotFoundException($resourceFile->getPath());
        }

        return $resourceFile->getPath();
    }

    /**
     * @param string $alias resource alias
     */
    public function moduleNameAndPath($alias){
        $isModuleResource = Strings::contains($alias, self::MODULE_SEPARATOR)
        && Strings::contains($alias, self::PATH_SEPARATOR)
        && strpos($alias, '.') < strpos($alias, '/');

        $module = APP_MODULE_NAME;
            $path = $alias;

        if($isModuleResource){
            list($module, $path) = explode(self::MODULE_SEPARATOR, $alias, 2);
            $path = trim($path, '/');
        }

        if(!Modules::instance()->exists($module)){
            throw new ModuleNotFoundException($module);
        }

        return [$module, $path];
    }

}
