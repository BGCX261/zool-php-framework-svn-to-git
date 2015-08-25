<?php

namespace zool\management;

use zool\util\Strings;

class AnnotationManager{

    /**
       * @var AnnotationManager singleton instance
       */
    private static $instance;

    /**
      * @return AnnotationManager instance
      */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new AnnotationManager;
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
     * @param array $annotationsDefinition definition
     * @param string $annotation annotation name
     * @return Annotation|NULL
     */
    public function getAnnotation($annotationsDefinition, $annotation, $target){

        foreach ($annotationsDefinition as $k => $annotDef){

            $annotationKey = array_keys($annotDef)[0];
            $values = $annotDef[$annotationKey];

            if($annotationKey == $annotation){

                $annotationClassName = $annotation;

                $annotationInstance = new $annotationClassName([], $target);

                foreach ($values as $prop => $value){
                    $annotationInstance->$prop = $value;
                }

                return $annotationInstance;
            }
        }

        return null;

    }

}