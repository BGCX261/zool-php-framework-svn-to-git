<?php

namespace zool\annotation;

use zool\util\Strings;

use zool\vendor\addendum\Annotation;
use zool\scope\ScopeType;

/** @Target("class") */
class Component extends Annotation{
    protected function checkConstraints($target) {
        if(Strings::isEmpty($this->value)){
            throw new AnnotationException("Component name cannot be empty.\n{$target->__toString()}");
        }
    }
}

/** @Target("property") */
class In extends Annotation{
    public $required = false;
}

/** @Target("property") */
class Out extends Annotation{
    public $required = false;
    public $scope = ScopeType::EVENT;

    protected function checkConstraints($target) {
        if(null == $this->scope || $this->scope == ScopeType::UNSPECIFIED){
            throw new AnnotationException("Scope name cannot be unspecified or stateless.\n{$target->__toString()}");
        }
    }
}

/** @Target("method") */
class Factory extends Annotation{
    public $scope = ScopeType::UNSPECIFIED;
}

/**
 * The target value setted from request value.
 *
 * @Target("property")
 **/
class RequestParam extends Annotation{
    public $required = false;
}

/**
 * Parameters of invoked method will come from request.
 * <p>
 * If property 'required' is true, the values strictly from request,
 * else the invoke can contain parameters, and just the missig parameters will come from request.
 * </p>
 *
 * @Target("method")
 **/
class RequestParameterized extends Annotation{
    public $required = false;
}

/** @Target("class") */
class Scope extends Annotation{
    public $value = ScopeType::UNSPECIFIED;
}

/**
 * @Target("method")
 **/
class Observer extends Annotation{
    protected function checkConstraints($target) {
        if(Strings::isEmpty($this->value)){
            throw new AnnotationException("Observer value cannot be empty.\n{$target->__toString()}");
        }
    }
    public $value;
    public $priority = 1;
}

/** @Target("property") */
class Logger extends Annotation{

}
