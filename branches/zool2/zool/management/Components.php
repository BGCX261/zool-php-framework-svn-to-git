<?php

namespace zool\management;

use zool\scope\ScopeType;

use zool\vendor\addendum\Annotation;

use zool\component\Component;

use zool\scope\Scopes;

use zool\exception\ZoolException;

use zool\deploy\Deployment;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Components{

    const PACKAGE_SEPARATOR = '.';

    /**
     *
     * @param unknown_type $component
     * @param unknown_type $byClass
     * @throws ComponentException
     * @return \zool\management\ComponentProxy proxy for founded component or null if not found
     */
    public static function getInstance($component, $byClass = false){

        $scopeValue = Scopes::instance()->get($component, null, false);

        if(null !== $scopeValue){
            return $scopeValue;
        }

        if($byClass){
            return self::getComponentByClass($component);
        }

        $component = self::getFullyQualifiedName($component);

        if(null === $component){
            return null;
            //throw new ComponentException("No component found: $component");
        }

        if(isset(Deployment::instance()->components[$component])){

            $componentDefinition = Deployment::instance()->components[$component];
            $className = $componentDefinition['class'];

            // instantiate class
            $componentInstance = new $className;

            $proxy = new ComponentProxy($componentInstance, $componentDefinition);

            $scope = self::getComponentScope($component);

            Scopes::instance()->setToScope($scope, $component, $proxy);

            return $proxy;

        }

        if(isset(Deployment::instance()->factories[$component])){
            return self::getFactory($component);
        }

        return null;

    }

    /**
     *
     * @param string $factory factory name
     * @return object instance of factory
     */
    private static function getFactory($factory){

        $factoryDefinition = Deployment::instance()->factories[$factory];
        $holderComponent = $factoryDefinition['component'];

        $componentInstance = self::getInstance($holderComponent);

        return call_user_func_array([$componentInstance, $factoryDefinition['method']], []);
    }

    /**
     *
     * @param string $simpleName
     * @return string full name of component
     */
    private static function getFullyQualifiedName($simpleName){

        // the name is fully qualified
        if(array_key_exists($simpleName, Deployment::instance()->components)
                || array_key_exists($simpleName, Deployment::instance()->factories)){
            return $simpleName;
        }

        foreach (Deployment::instance()->components as $fullyQualifiedName => $def){
            if($simpleName == self::getSimpleName($fullyQualifiedName)){
                return $fullyQualifiedName;
            }
        }

        foreach (Deployment::instance()->factories as $fullyQualifiedName => $def){
            if($simpleName == self::getSimpleName($fullyQualifiedName)){
                return $fullyQualifiedName;
            }
        }

        return null;

    }

    /**
     *
     * @param string $component fully qualified name of component
     * @return string simple name of component (without package prefix)
     */
     private static function getSimpleName($component){
        return substr($component, strrpos($component, self::PACKAGE_SEPARATOR) + 1 );
    }


    /**
     *
     * @param unknown_type $class
     * @return NULL|object instance of component
     */
    public static function getComponentByClass($class){

        $components = Deployment::instance()->components;

        foreach ($components as $componentName => $definition){

            if($definition == $class){
                return self::getInstance($componentName);
            }

        }

        throw new ComponentException("No component with class: $class");

    }



    /**
     * Returns class name of component.
     * @param string $componentName name of component
     * @return string class name of component
     */
    public static function getClass($componentName){

        $componentName = self::getFullyQualifiedName($componentName);

        if(isset(Deployment::instance()->components[$componentName])){

            return Deployment::instance()->components[$componentName]['class'];

        }

        throw new ComponentException("Undefined component: $componentName");
    }

    /**
     *
     * @param string $componentName name of component
     * @throws ComponentException if component name not found
     * @return int scope type
     */
    public static function getComponentScope($component){

        $component = self::getFullyQualifiedName($component);

        if(isset(Deployment::instance()->components[$component])){

            $componentDefinition = Deployment::instance()->components[$component];
            $componentAnnotations = $componentDefinition['annotations'];

            $scopeAnnotation = AnnotationManager::instance()->getAnnotation($componentAnnotations, 'Scope', $component);

            if(null !== $scopeAnnotation ){
                return $scopeAnnotation->value;
            }

            return ScopeType::UNSPECIFIED;
        }

        throw new ComponentException("Undefined component: $component");
    }


    /**
     * Calls static component method.
     *
     * @param string $componentName name of component
     * @param string $methodName name of method
     * @param array $parameters array of paramenters
     * @return mixed method result
     */
    public static function callStaticMethod($componentName, $methodName, $parameters = []){
        $class = self::getClass($componentName);
        return call_user_func_array([$class, $methodName], $parameters);
    }

}