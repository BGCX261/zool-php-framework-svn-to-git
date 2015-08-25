<?php

namespace zool\management;

use zool\util\log\LogProvider;

use zool\http\Request;

use zool\scope\Scopes;

use zool\component\Component;

/**
 *
 * Component proxy helps Zool to handle components as a entity.
 * <p>The method calling is the main motor of the component manipulation. The bijection is around the call.</p>
 * <p>The injection happen before call the method, the outjection after call.</p>
 * <p>After outjection the object will have the setted values.</p>
 *
 * @author Zsolt Lengyel
 *
 */
class ComponentProxy implements \ArrayAccess, \IteratorAggregate {

    private $__proxydelegate__ = null;
    private $__proxyclass__ = null;
    private $__proxydefinitions__ = [];

    /**
     * Creates proxy
     * @param zool\component\Component $instance
     */
    public function __construct(Component $instance, $definitions){
        $this->__proxydelegate__ = $instance;
        $this->__proxyclass__ = get_class($instance);
        $this->__proxydefinitions__ = $definitions;
    }

    public function __call($method, $paramters){

        InjectionManager::instance()->handle($this->__proxydelegate__, $this->__proxydefinitions__);

        if(isset($this->__proxydefinitions__['methods'][$method])){

            $methodDefinitions = $this->__proxydefinitions__['methods'][$method];
            $paramters = ParameterInjectionManager::instance()->getParametersToCall($this->__proxydelegate__, $method, $methodDefinitions , $paramters);

        }

        $result = call_user_func_array([$this->__proxydelegate__, $method], $paramters);

        OutjectionManager::instance()->handle($this->__proxydelegate__, $this->__proxydefinitions__);

        return $result;
    }

    public static function __callStatic($method, $paramters){
       throw new ComponentException('Do not call component\'s static method. ('.$method.')');
    }

    public function __get($name){
        if(in_array($name, ['__proxydelegate__', '__proxyclass__', '__proxydefinitions__'])){
            return $this->$name;
        }
        return $this->__proxydelegate__->$name;
    }

    public function __set($prop, $value){
        if(in_array($prop, ['__proxydelegate__', '__proxyclass__', '__proxydefinitions__'])){
            $this->$prop = $valuie;
            return;
        }
        $this->__proxydelegate__->$prop = $value;
    }

    public function __isset($name){
        return isset($this->__proxydelegate__->$name);
    }

    public function __unset($name){
        unset($this->__proxydelegate__->$name);
    }

    public function offsetExists($key) {
        return isset($this->__proxydelegate__[$key]);
    }

    public function offsetUnset($key) {
        unset($this->__proxydelegate__[$key]);
    }

    public function offsetGet($key) {
        return $this->__proxydelegate__[$key];
    }

    public function offsetSet($key, $value) {
        $this->__proxydelegate__[$key] = $value;
    }

    public function getIterator() {
        $this->__proxydelegate__->getIterator();
    }

    public function getClass(){
        return $this->__proxyclass__;
    }



}