<?php

namespace zool\deploy;

class Deployment extends \ArrayObject{

    const DEPLOYMENT_DESCRIPTOR = 'deployment.php';

    private static $instance = null;

    public static function  instance(){
        if(null == self::$instance){
            self::$instance = new Deployment();
            self::$deployment = require_once self::getDeploymentDescriptorPath();
        }

        return self::$instance;
    }

    private static $deployment = null;

    public function __construct(){
    }

    public function __get($name){
        return self::$deployment[$name];
    }

    public function offsetGet($name) {
        return self::$deployment[$name];
    }
    public function offsetSet($name, $value) {
        self::$deployment[$name] = $value;
    }
    public function offsetExists($name) {
        return isset(self::$deployment[$name]);
    }
    public function offsetUnset($name) {
        unset(self::$deployment[$name]);
    }

    public static function getDeploymentDescriptorPath(){
        return RUNTIME_PATH.DS.self::DEPLOYMENT_DESCRIPTOR;
    }



}