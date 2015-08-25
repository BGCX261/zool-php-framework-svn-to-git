<?php

namespace zool\component;

use zool\base\Accessable;


/**
 * Parent class of all component.
 *
 * @author Zsolt Lengyel
 *
 */
abstract class Component extends Accessable{

    public final function __construct(){
        $this->init();
    }

    protected function init(){

    }

    public final function __destruct(){
        $this->destroy();
    }

    protected function destroy(){

    }

    public function getClass(){
        return get_called_class();
    }

}