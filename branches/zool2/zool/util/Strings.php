<?php

namespace zool\util;

/**
 * String utils.
 *
 * @author Zsolt Lengyel
 *
 */
class Strings{

    /**
     *
     * @param string $string context
     * @param string $begin needle
     * @return boolean true if context begins with needle
     */
    public static function startsWith($string, $begin){
        return substr($string, 0, strlen($begin)) == $begin;
    }

    /**
     *
     * @param string $string context
     * @param array $prefixes
     * @return boolean true if context starts with one of the prefixes
     */
    public static function startsWithOneOf($string, $prefixes){
        foreach ($prefixes as $prefix){
            if(self::startsWith($string, $prefix)){
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param string $string context
     * @param string $end end of string
     * @return boolean true if context ends with the end string
     */
    public static function endsWidth($string, $end){
        return substr($string, -1*strlen($end)) == $end;
    }

    /**
     *
     * @param string $string
     * @param atring $needed
     * @return boolean true if string contains the second parameter
     */
    public static function contains($string, $needed){
        return -1 < strstr($string, $needed);
    }

    /**
     *
     * @param string $string
     * @return boolean true if string is null or empty string
     */
    public static function isEmpty($string){
        return null === $string || $string === '';
    }

    /**
     * Sets first parameter's value idf it is empty.
     * @param string $string
     * @param string $value
     */
    public static function setIfEmpty(&$string, $value){
        if(self::isEmpty($string))
            $string = $value;
    }

    public static function stillTheFirstOccurence($string, $needed){
        if(!self::contains($string, $needed)) return $string;

        return substr($string, 0, strpos($string, $needed));
    }

    public static function splitBy($string, $separator){
        return explode($separator, $string, 2);
    }

}