<?php

namespace zool\deploy;

use zool\base\module\ModuleInfo;

use zool\util\time\Watch;

use zool\util\log\LogProvider;

use zool\util\log\Log;

use zool\util\Reflection;

use zool\exception\ZoolException;

use zool\vendor\addendum\Annotation;

use zool\vendor\addendum\ReflectionAnnotatedProperty;

use zool\vendor\addendum\ReflectionAnnotatedMethod;

use zool\vendor\addendum\ReflectionAnnotatedClass;

use zool\util\Strings;

use zool\file\File;

use zool\file\Directory;




/**
 * Creates deployment descriptor of an Zool application.
 *
 * @author lzsolt
 *
 */
class ApplicationDeployer{



    const COMPONENT_ANNOTATION = 'zool\\annotation\\Component';

    const OBSERVER_ANNOTATION = 'zool\\annotation\\Observer';

    const FACTORY_ANNOTATION = 'zool\\annotation\\Factory';

    const COMPONENT_PARENT_CLASS = 'zool\\component\\Component';

    private static $APPLICATIONS = ['app', 'zool'];


    /**
     *
     * @var Log
     */
    private $log;


    /**
     * Absulute path of application.
     * @var string
     */
    private $applicationPaths;

    /**
     * Information about components,
     * @var array
     */
    private $components = [];

    /**
     * Definiton of event observers
     * @var array
     */
    private $events = [];

    /**
     * Definition of factories.
     * @var array
     */
    private $factories = [];

    /**
     * Registered modules.
     *
     * @var
     */
    private $modules = [];

    private $models = [];


    /**
     * Default constructor.
     *
     * @param string $applicationPath absolute path of Zool application to deploy
     */
    public function __construct($applicationPaths){
        $this->applicationPaths = $applicationPaths;
        $this->log = LogProvider::forClass($this, false);

    }

    private function clear(){
        $this->components = [];
        $this->factories = [];
        $this->events = [];
        $this->modules = [];
        $this->models = [];
    }

    /**
     *
     * @param string $deploymentDescriptorPath absolute path of descriptor file
     */
    public function deploy(){

        $this->log->console("DEPLOY =============================================================\n\n");
        $watch = new Watch();
        $watch->start();

        $this->clear();

        $deployment = [];

        foreach($this->applicationPaths as $application => $applicationPath){

            $moduleDir = new Directory($applicationPath);

            $this->scanApplications($application, $applicationPath);

            if($moduleDir->exists()){
                $moduleDeployer = new ModuleDeployer($moduleDir->getPath(), true);

                $deployment[$application] = $moduleDeployer->deploy();

            }
        }

        $this->scanModules($deployment, '');

        $arrayIterator = new \RecursiveArrayIterator($deployment);
        $deploymentIterator = new \RecursiveIteratorIterator($arrayIterator);

        foreach ($deploymentIterator as $key => $classPath){
            if(is_file($classPath))
                include_once $classPath;
        }

        $this->scanComponents();

        $this->exportDeploymentDescriptor();

        $this->log->console("Deploy time: \t {$watch->stop()}\n\n");

    }

    private function scanApplications($app, $appPath){

        $info = [];

        $moduleInfoFile = new File("{$appPath}/".ModuleInfo::MODULE_INFO_FILENAME);

        if($moduleInfoFile->exists()){
            $info = $moduleInfoFile->includeFile();
        }else{
            $this->log->console("Missing module info file in {$appPath}", null, Log::WARNING);
        }

        $this->addModule($app,['path'=>$appPath, 'info'=>$info]);
    }

    /**
     * @param array $deployment
     * @return void
     */
    private function scanModules($deployment, $modulePrefix){

        $this->scanModels($deployment, $modulePrefix);

        foreach ($deployment as $module){
            if(array_key_exists('modules', $module)){

                foreach ($module['modules'] as $subModuleName => $subModule){
                    $this->log->console("Module:  \t $subModuleName");
                    $this->addModule($subModuleName,
                            [
                            'path'=> Directory::normalize($subModule['path']),
                            'info'=> $subModule['info']
                            ]);

                    $this->scanModules([$subModule], $subModuleName);
                }

            }

        }

    }


    /**
     *
     */
    private function addModule($name, $definition){

        if(array_key_exists($name, $this->modules)){
            throw new DeploymentException("Multiple module definition. ".
                    $this->modules[$name]['path'] .'; ' . $definition['path']);
        }

        $this->modules[$name] = $definition;

    }

    /**
     * @param array $deployment
     * @return void
     */
    private function scanModels($modules, $moduleName){

        foreach ($modules as $module){
            if(array_key_exists('models', $module)){

                foreach ($module['models'] as $modelName => $modelPath){

                    $modelClass = $modelName;

                    if(!Strings::isEmpty($modelName)){
                        $modelClass = str_replace('module\\', '', $modelClass);
                    }

                    $this->models[] = $modelClass;
                    $this->log->console("Model:    \t " . $modelClass);

                }
            }

        }

    }

    /**
     * Scans for declared components between declared classes.
     */
    private function scanComponents(){

        $enabledPrefixes = array_merge(self::$APPLICATIONS, array_keys($this->modules));

        foreach (get_declared_classes() as $class){

            if(Strings::startsWithOneOf($class, $enabledPrefixes)){

                $classReflection = new ReflectionAnnotatedClass($class);

                if(Reflection::inheritFrom($class, self::COMPONENT_PARENT_CLASS))
                {

                    if(!$classReflection->hasAnnotation(self::COMPONENT_ANNOTATION)){
                        $this->log->console("DEFINITION ERROR: $class extends Component, but has no annotation 'Component'");
                        continue;

                    }

                    $componentAnnotation =  $classReflection->getAnnotation(self::COMPONENT_ANNOTATION);

                    $componentName = $componentAnnotation->value;

                    $this->log->console( "Component:  \t $componentName -> $class");

                    if(array_key_exists($componentName, $this->components)){
                        throw new DeploymentException("Multiple component definition: ".$this->components[$componentName]['class'] ." -> ". $class);
                    }


                    $this->components[$componentName] = [];

                    $this->components[$componentName]['class'] = $class;
                    $this->components[$componentName]['annotations'] = [];

                    foreach ($classReflection->getAllAnnotations() as $annot){
                        $this->components[$componentName]['annotations'][] = $this->exportAnnotation($annot);
                    }


                    $this->components[$componentName]['properties'] = [];
                    $this->components[$componentName]['methods'] = [];

                    foreach ($classReflection->getProperties() as $reflectProp){

                        $reflectAnnotations = $reflectProp->getAllAnnotations();

                        if(!empty($reflectAnnotations)){
                            $this->components[$componentName]['properties'][$reflectProp->name] = [];
                            $this->components[$componentName]['properties'][$reflectProp->name]['annotations'] = [];

                            foreach ($reflectAnnotations as $propAnnot){
                                $this->components[$componentName]['properties'][$reflectProp->name]['annotations'][] = $this->exportAnnotation($propAnnot);
                            }
                        }
                    }

                    foreach ($classReflection->getMethods() as $reflectMethod){
                        $this->processMethod($componentName, $reflectMethod);
                    }

                }


            }
        }
    }


    /**
     * Processes reflection method.
     *
     * @param string $componentName name of component
     * @param ReflectionProperty $reflectMethod reflection method
     *
     * @return void
     */
    private function processMethod($componentName, $reflectMethod){

        $reflectAnnotationsList = $reflectMethod->getAllAnnotations();

        if($reflectMethod->isPublic() && !empty($reflectAnnotationsList)){

            foreach ($reflectAnnotationsList as $methodAnnot){
                $annotationClass = get_class($methodAnnot);

                if($annotationClass == self::OBSERVER_ANNOTATION){

                    $eventName = $methodAnnot->value;
                    $this->addEventListener($eventName,
                            ['component'=>$componentName,
                            'method'=>$reflectMethod->getName(),
                            'static'=>$reflectMethod->isStatic()], $methodAnnot->priority);

                }elseif($annotationClass == self::FACTORY_ANNOTATION){

                    $this->addFactory($methodAnnot->value,
                            ['component'=>$componentName,
                            'method'=>$reflectMethod->getName(),
                            'static'=>$reflectMethod->isStatic(),
                            'scope'=>$methodAnnot->scope]);

                }else{

                    if(!isset( $this->components[$componentName]['methods'][$reflectMethod->name])
                            || !isset($this->components[$componentName]['methods'][$reflectMethod->name]['annotations'])){
                        $this->components[$componentName]['methods'][$reflectMethod->name] = [];
                        $this->components[$componentName]['methods'][$reflectMethod->name]['annotations'] = [];
                    }

                    $this->components[$componentName]['methods'][$reflectMethod->name]['annotations'][] = $this->exportAnnotation($methodAnnot);
                }
            }
        }

    }

    /**
     * @return void
     */
    private function addEventListener($event, $listener, $priority){

        if(!array_key_exists($event, $this->events)){
            $this->events[$event] = [];
        }

        $listener['priority'] = $priority;

        $this->events[$event][] = $listener;

    }

    /**
     * @return void
     */
    private function addFactory($factoryName, $factory){

        if(array_key_exists($factoryName, $this->factories)){
            $oldFactory = $this->factories[$factoryName];

            throw new DeploymentException("Multiple factory definition: $factoryName.
                    Defines: {$oldFactory['component']}->{$oldFactory['method']}
            \n Redefines: {$factory['component']}->{$factory['method']}");
        }

        $this->log->console( "Factory:   \t $factoryName");
        $this->factories[$factoryName] = $factory;

    }


    /**
     * Writes the collected data to file.
     * @return void
     */
    private function exportDeploymentDescriptor(){

        // sort events by priority
        uasort($this->events, function($a, $b){

            if($a['priority'] == $b['priority']) return 0;
            return $a['priority'] > $b['priority'] ? -1 : 1;

        });

            $deployment = [
            'modules'=>$this->modules,
            'models'=>$this->models,
            'components'=>$this->components,
            'factories'=>$this->factories,
            'events'=>$this->events
            ];

            $deploymentScript = '<?php return '. var_export($deployment, true) .";\n";


            $runtimeDir = new Directory(RUNTIME_PATH);
            $runtimeDir->create();

            $deploymentDescriptor = new File(self::getDeploymentDescriptorPath());
            $deploymentDescriptor->touch($deploymentScript);

    }

    /**
     * @param $annotation an annotation object
     * @return void
     */
    private function exportAnnotation($annotation){
        $className = get_class($annotation);
        $values = [];
        foreach ($annotation as $p => $val){

            if(is_object($val)){

                $values[$p] = $this->exportAnnotation($val);

            }elseif(is_array($val)){

                $values[$p] = $this->exportArrayOfAnnotation($val);

            }else{

                $values[$p] = $val;

            }

        }


        return [$className=>$values];
    }

    /**
     * Transforms array of annotations to simple array.
     * @return void
     */
    private function exportArrayOfAnnotation($value){
        $result = [];
        foreach ($value as $key => $parameter){
            if(is_object($parameter) && $parameter instanceof Annotation){

                $result[] = $this->exportAnnotation($parameter);

            }elseif(is_array($parameter)){

                $result[] = $this->exportArrayOfAnnotation($parameter);

            }else{

                $result[] = $parameter;

            }
        }
        return $result;
    }

    /**
     * @return string path of deployment descriptor
     */
    public static function getDeploymentDescriptorPath(){
        return Deployment::getDeploymentDescriptorPath();
    }


}