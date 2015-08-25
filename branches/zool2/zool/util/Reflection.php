<?php

namespace zool\util;

use zool\vendor\addendum\ReflectionAnnotatedMethod;
use zool\vendor\addendum\ReflectionAnnotatedProperty;
use zool\vendor\addendum\ReflectionAnnotatedClass;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Reflection{

    /**
     *
     * @param object|string $classOrObject children
     * @param string $parentClass parent class name
     */
    public static function inheritFrom($classOrObject, $parentClass){

        $child = is_object($classOrObject) ? get_class($classOrObject) : $classOrObject;

        if(!class_exists($child))return false;

        $reflection = new \ReflectionClass($child);

        $parent = $reflection->getParentClass();
        while(is_object($parent) && $parent->getName() != '\stdObject'){

            if($parent->getName() == $parentClass){
                return true;
            }
            $parent = $parent->getParentClass();

        }

        return false;
    }

    /**
     *
     * @param string|object $classOrObject
     * @param string $method
     * @return \zool\vendor\addendum\ReflectionAnnotatedMethod
     */
    public static function getReflectionMethod($classOrObject, $method){
        return new ReflectionAnnotatedMethod(self::getClass($classOrObject), $method);
    }

    /**
     *
     * @param string|object $classOrObject
     * @param string $method
     * @return \zool\vendor\addendum\ReflectionAnnotatedProperty
     */
    public static function getReflectionProperty($classOrObject, $property){
        return new ReflectionAnnotatedProperty(self::getClass($classOrObject), $property);
    }

    /**
     *
     * @param string|object $classOrObject
     * @return \zool\vendor\addendum\ReflectionAnnotatedClass
     */
    public static function getReflectionClass($classOrObject){
        return new ReflectionAnnotatedClass(self::getClass($classOrObject));
    }

    /**
     *
     * @param string|object $classOrObject
     * @param string $method
     * @return array array of \ReflectionParameter
     */
    public static function getReflectionParameters($classOrObject, $method){
        return self::getReflectionMethod($classOrObject, $method)->getParameters();
    }

    /**
     * Resolves parameter's class. If it is a class return it.
     * @param string|object $classOrObject
     * @return string class name
     */
    public static function getClass($classOrObject){
        return is_object($classOrObject) ? get_class($classOrObject) : $classOrObject;
    }

}