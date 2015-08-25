<?php

namespace zool\deploy;

use zool\file\File;

use zool\util\log\Log;

use zool\util\log\LogProvider;

use zool\base\module\ModuleInfo;

use zool\file\Directory;

/**
 * Deploys a module.
 *
 * @author Zsolt Lengyel
 *
 */
class ModuleDeployer {


    const PHP_FILE_FILTER =  '/^.+\.php$/i';

    const COMPONENTS_FOLDER = 'component';

    const MODULES_FOLDER = 'module';

    const MODELS_FOLDER = 'model';


    /**
     * Absulute path of application.
     * @var string
     */
    private $modulePath;

    /**
     *
     * @var Log
     */
    private $log;

    private $baseModule;

    /**
     * Default constructor.
     *
     * @param string $modulePath absolute path of Zool application to deploy
     */
    public function __construct($modulePath, $baseModule = false){
        $this->modulePath = Directory::normalize($modulePath);
        $this->log = LogProvider::forClass($this, false);
        $this->baseModule = $baseModule;
    }

    /**
     *
     * @param string $deploymentDescriptorPath absolute path of descriptor file
     * $return array module deployment descriptor
     */
    public function deploy(){

        $compontens = [];
        $models = [];
        $modules = [];
        $moduleInfo = [];

        $moduleInfoFile = new File("{$this->modulePath}/".ModuleInfo::MODULE_INFO_FILENAME);

        if($moduleInfoFile->exists()){
            $moduleInfo = $moduleInfoFile->includeFile();
        }else{
            $this->log->console("Missing module info file in {$this->modulePath}", null, Log::WARNING);
        }



        $componentsDir = new Directory("{$this->modulePath}/".self::COMPONENTS_FOLDER);

        if($componentsDir->exists()){
            $compontens = $componentsDir->getFiles(true, self::PHP_FILE_FILTER);
        }

        $modelsDir = new Directory("{$this->modulePath}/".self::MODELS_FOLDER);

        if($modelsDir->exists()){
            $modelsTmp = $modelsDir->getFiles(true, self::PHP_FILE_FILTER);
            foreach ($modelsTmp as $modelPath){

                $class = substr($modelPath, strrpos($this->modulePath, DS) + 1 ); // remove path
                $class = substr($class, 0, -4); // remove .php extendsion
                $class = str_replace(DS, '\\', $class); // direectory separator to namespace separator

                $models[$class] = $modelPath;

            }
        }

        $modulesDir = new Directory("{$this->modulePath}/".self::MODULES_FOLDER);

        if($modulesDir->exists()){

            foreach ($modulesDir->getDirectories() as $subModulePath){

                $moduleName = substr(str_replace($modulesDir->getPath(), '', $subModulePath), 1);

                $subModuleDeployer = new ModuleDeployer($subModulePath);
                $modules[$moduleName] = $subModuleDeployer->deploy();

            }
        }

        return [
        'path'=>$this->modulePath,
        'compontents'=>$compontens,
        'modules'=>$modules,
        'models'=>$models,
        'info'=>$moduleInfo
        ];

    }

}