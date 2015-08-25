<?php

namespace zool\util\log;

use zool\Zool;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class LogProvider{

    /**
     * @param object|string $classOrObject classname or object
     * @return \zool\util\log\Log logger
     */
    public static function forClass($classOrObject, $toFile = true){

        $logKey = $classOrObject;

        if(is_object($classOrObject) || class_exists($classOrObject)){
            $class = is_object($classOrObject) ? get_class($classOrObject) : $classOrObject;
            $logKey = (new \ReflectionClass($class))->getShortName();
        }

        $path = Zool::isActiveApplication() ? Zool::app()->config->log->path : 'log/log';

        return new Log($logKey, $toFile ? $path : null);
    }

}