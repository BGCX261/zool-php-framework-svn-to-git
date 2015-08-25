<?php

namespace zool\management;

use zool\http\Request;

use zool\util\Reflection;

class ParameterInjectionManager{

    const REQUEST_PARAMETERIZED_ANNOTATION = 'zool\\annotation\\RequestParameterized';

    /**
     * @var ParameterInjectionManager singleton instance
     */
    private static $instance;

    /**
     * @return ParameterInjectionManager instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new ParameterInjectionManager;
        }
        return self::$instance;
    }

    /**
     * Singleton constructor.
     */
    private function __construct(){
    }

    /**
     *
     * @param string|Component $component
     * @param string $method
     * @param array $paramters
     * @return array invoke parameters
     */
    public function getParametersToCall($component, $method, $definitions, $parameters){

        $reflectionParameters = Reflection::getReflectionParameters($component, $method);

        if(isset($definitions['annotations']) && !empty($reflectionParameters)){

            $target = Reflection::getReflectionMethod($component, $method);
            $parameters = $this->requestParameterized($definitions, $target, $reflectionParameters, $parameters);

        }
        return $parameters;

    }

    /**
     *
     * @param array $definitions
     * @param ReflectionMethod $target
     * @param array $reflectionParameters
     * @param array $parameters
     * @return array
     */
    private function requestParameterized($definitions, $target, $reflectionParameters, $parameters){

        $annotation = AnnotationManager::instance()->getAnnotation($definitions['annotations'], self::REQUEST_PARAMETERIZED_ANNOTATION, $target);
        if(null !== $annotation){

            $startIndex = $annotation->required ? 0 : count($parameters);

            for($i = $startIndex; $i < count($reflectionParameters); $i++){

                $reflectionParameter = $reflectionParameters[$i];

                $paramName = $reflectionParameter->getName();

                $paramValue = Request::instance()->get($paramName);

                if($paramValue === null && $reflectionParameter->isDefaultValueAvailable()){

                    $paramValue = $reflectionParameter->getDefaultValue();

                }elseif($paramValue === null && !$reflectionParameter->isOptional()){

                    throw new InjectionException("No request parameter for
                    {$reflectionParameter->getDeclaringClass()->getName()}::{$reflectionParameter->getDeclaringFunction()->getName()}#{$paramName}");

                }

                $parameters[] = $paramValue;
            }


            return $parameters;

        }

    }



}

