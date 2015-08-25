<?php

namespace zool\application;

use zool\exception\NotSupportedFunction;

use zool\base\Accessable;


/**
 *
 * @author Zsolt Lengyel
 *
 */
class Configuration extends Accessable implements \IteratorAggregate, \ArrayAccess{

    private $configuration = array();


    public function __construct(array $confArray){
        foreach($confArray as $key => $conf){
            $this->configuration[$key] = is_array($conf) ? new Configuration($conf) : $conf;
        }

    }

    public function getIterator () {
        return new ArrayIterator($this->configuration);
    }

    public function get($name, $default = null){
        return isset($this->configuration[$name]) ? $this->configuration[$name] : $default;
    }

    public function __get($name){
        return $this->get($name);
    }

    public function asArray(){
        $result = [];

        foreach ($this->configuration as $key => $conf){
            if($conf instanceof Configuration){
                $result[$key] = $conf->asArray();
            }else{
                $result[$key] = $conf;
            }
        }

        return $result;
    }

    public function offsetGet($key){
        return $this->configuration[$key];
    }

    public function offsetExists($key){
        return isset($this->configuration[$key]);
    }

    public function offsetSet($offset, $value){
        throw new NotSupportedFunction();
    }

    public function offsetUnset($offset){
        throw new NotSupportedFunction();
    }

    //   public function set($property, $value){
    //     $prevValue = $this->get($property);

    //     $this->configuration[$property] = $value;

    //     return $prevValue;
    //   }

    //   public function __set($property, $value){
    //     return $this->set($property);
    //   }



}