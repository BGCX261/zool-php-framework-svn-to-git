<?php

namespace xul\form;

use xul\form\abstracts\AbstractFormElement;

use zool\tools\ZUniqueIdGenerator;

use zool\aspects\ZElement;

class Form extends ZElement{

  public $id;

  private $formElements = array();

  public function init(){
    if($this->id == null){
     $this->id = 'form'.ZUniqueIdGenerator::next();
    }
    parent::init();
  }

  protected function getUniqueElementId(){
    return $this->id.'_'.ZUniqueIdGenerator::next();
  }

  public function registerElement(AbstractFormElement $element){
    if($element->id == null){
      $element->id = $this->getUniqueElementId();
    }
    $this->formElements[$element->valueBind.':'.$element->valueBindScope] = $element;
  }

  public function render(){
    $o = parent::render();

    $o .= $this->renderScript();
    $o .=$this->renderedChildren();

    return $o;
  }

  public function getFormJSFunction(){
    return "function(){Zool.byId('".$this->id ."_formData').doCommand(); return Zool.forms.$this->id();}";
  }

  private function renderScript(){
   /* return "<script type=\"application/javascript\"><![CDATA[
    var {$this->id}_formData = function(){return {{$this->getFormDataString()}}.zoolForm;};
    ]]>
    </script>\n";
    */
    return "<command id=\"{$this->id}_formData\" oncommand=\"Zool.forms.$this->id=function(){return {$this->getFormDataString()};}\" />";
  }

  public function getFormDataString(){
    $elementGetters = array();
    foreach ($this->formElements as $binding => $element){
      $elementGetters[] = "'$binding':".$element->getJSValueGetter();
    }

    return "{". implode(',',$elementGetters) ."}";
  }

}