<?php

namespace zool\base;

use zool\exception\ReflectionException;

/**
 *
 * @author Zsolt Lengyel
 *
 */
abstract class Accessable{

    public $__zoid__ = null;

    public function __get($name){

        $getter='get'.ucfirst($name);

        if(method_exists($this,$getter))
        {
            // getting a property
            return $this->$getter();
        }elseif(array_key_exists($name, get_object_vars($this))){
            return $this->$name;
        }else{
            throw new ReflectionException(get_class($this). '->'.$name. ' is undefined.');
        }
    }

    public function __set($name, $value){

        $setter = 'set'.ucfirst($name);
        if(method_exists($this, $setter)){

            $this->$setter($value);

        }elseif(array_key_exists($name, get_object_vars($this))){

            $this->$name = $value;

        }elseif(method_exists($this, 'get'.ucfirst($name))){
            throw new ReflectionException(get_class($this). '->'.$name. ' is readonly.');
        }else{
            throw new ReflectionException(get_class($this). '->'.$name. ' is undefined.');
        }
    }

    public function getOid(){
        if(is_null($this->__zoid__)){
            ob_start(); var_dump($this, microtime());
            $stamp = ob_get_clean();
            $this->__zoid__ = sha1(spl_object_hash($this) . $stamp);
        }
        return $this->__zoid__;
    }

    public function __toString(){
        return get_class($this);
    }

}
