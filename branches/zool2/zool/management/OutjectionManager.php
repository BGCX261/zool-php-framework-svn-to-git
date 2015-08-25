<?php

namespace zool\management;

use zool\util\Reflection;

use zool\scope\Scopes;

use zool\management\AnnotationManager;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class OutjectionManager{

    const OUT_ANNOTATION = 'zool\\annotation\\Out';

    /**
     * OutjectionManager singleton instance
     */
    private static $instance;

    /**
     * @return OutjectionManager instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new OutjectionManager;
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
     * @param Component $component
     * @param array $compoentAnnotations
     * @throws OutjectionException when error happened while outjection property
     */
    public function handle($component, $compoentAnnotations){

        $properties = $compoentAnnotations['properties'];

        foreach ($properties as $property => $propertyDefinition){

            $annotations = $propertyDefinition['annotations'];

            $outAnnotation = AnnotationManager::instance()->getAnnotation($annotations, self::OUT_ANNOTATION, $this->getTargetReflection($component, $property));

            if(null !== $outAnnotation){

                $outjectKey = $outAnnotation->value === null ? $property : $outAnnotation->value;

                if($outAnnotation->required && $component->$property === null){
                    $class = get_class($component);
                    throw new OutjectionException("{$class}::$property outjection value is required.");
                }

                Scopes::instance()->setToScope($outAnnotation->scope, $outjectKey, $component->$property);

            }

        }
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