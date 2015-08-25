<?php

namespace web\control;
use zool\aspects\ZElement;

class Button extends ZElement{

    public $value = 'OK';

    /**
     * @Method
     */
    public $action;

    public function init(){
        $this->meta->name = 'input';
    }

    public function render(){
        parent::render();
        $this->action->run();
        $o = '<'.$this->fullname.' type="button" value="'. $this->value . '"/>';
        return $o;
    }
}