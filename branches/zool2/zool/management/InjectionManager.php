<?php

namespace zool\management;

use zool\util\Reflection;

use zool\http\Request;

use zool\util\log\Log;

use zool\util\log\LogProvider;

use zool\management\AnnotationManager;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class InjectionManager{

    const IN_ANNOTATION = 'zool\\annotation\\In';
    const REQUEST_PARAM_ANNOTATION = 'zool\\annotation\\RequestParam';
    const LOGGER_ANNOTATION = 'zool\\annotation\\Logger';

    /**
     * InjectionManager singleton instance
     */
    private static $instance;

    /**
     * @return InjectionManager instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new InjectionManager;
        }
        return self::$instance;
    }


    /**
     *
     * @var AnnotationManager manager
     */
    private $annotationManager;

    /**
     * Singleton constructor.
     */
    private function __construct(){
        $this->annotationManager = AnnotationManager::instance();
    }

    /**
     *
     * @param zool\component\Component $component
     * @param array $annotations
     * @param $afterCall if true, the values will be null
     * @throws InjectionException
     */
    public function handle($component, $compoentAnnotations, $afterCall = false){

        $properties = $compoentAnnotations['properties'];

        foreach ($properties as $property => $propertyDefinition){

            $annotations = $propertyDefinition['annotations'];


            $this->in($component, $property, $annotations, $afterCall);

            $this->requestParam($component, $property, $annotations, $afterCall);

            $this->logger($component, $property, $annotations, $afterCall);



        }
    }

    private function in($component, $property, $annotations, $afterCall){

        $inAnnotation = $this->annotationManager->getAnnotation($annotations, self::IN_ANNOTATION, $this->getTargetReflection($component, $property));
        if(null !== $inAnnotation){

            if($afterCall){
                $this->setToNull($component, $property);
                return;
            }

            $injectKey = $inAnnotation->value === null ? $property : $inAnnotation->value;
            $injectValue = Components::getInstance($injectKey);

            if($inAnnotation->required && $injectValue === null){
                $class = get_class($component);
                throw new InjectionException("{$class}::$property injection value is required.");
            }

            $class = get_class($component);

            $component->$property = $injectValue;

        }
    }

    private function requestParam($component, $property, $annotations, $afterCall){

        $reqAnnotation =  $this->annotationManager->getAnnotation($annotations, self::REQUEST_PARAM_ANNOTATION, $this->getTargetReflection($component, $property));
        if(null !== $reqAnnotation){

            if($afterCall){
                $this->setToNull($component, $property);
                return;
            }

            $injectKey = $reqAnnotation->value === null ? $property : $reqAnnotation->value;
            $injectValue = Request::instance()->get($injectKey);

            if($reqAnnotation->required && $injectValue === null){
                $class = get_class($component);
                throw new InjectionException("{class}::$property injection value is required.");
            }

            $component->$property = $injectValue;


        }
    }

    private function logger($component, $property, $annotations, $afterCall){

        $logAnnotation =  $this->annotationManager->getAnnotation($annotations, self::LOGGER_ANNOTATION, $this->getTargetReflection($component, $property));
        if(null !== $logAnnotation){

            if($afterCall){
                $this->setToNull($component, $property);
                return;
            }

            $logkey = $logAnnotation->value === null ? $component : $logAnnotation->value;
            $injectValue = LogProvider::forClass($logkey);
            $component->$property = $injectValue;

        }
    }

    /**
     *
     * @param Component $component component
     * @param string $property name of property
     */
    private function setToNull($component, $property){
        $component->$property = null;
    }

    /**
     *
     * @param string|object $component
     * @param string $property
     * @return \zool\vendor\addendum\ReflectionAnnotatedProperty
     */
    private function getTargetReflection($component, $property){
        return Reflection::getReflectionProperty($component, $property);
    }



}