<?php

namespace xul\form;

use zool\aspects\ZElementException;

use xul\form\abstracts\AbstractFormElement;

class Button extends AbstractFormElement{

  public $label = 'Xul';
  public $id = null;
  public $disableOnRequest = true;
  public $oncomplete = null;
  public $disabled = null;
  public $flex = null;

  /**
   * @Method
   */
  public $action;
  public $reRender = '';

  public function init(){
    $this->meta->name = 'button';

    $formData = 'zoolForm: '.$this->getFormParent()->getFormJSFunction();

    if($this->action != null){
      // If you want to set an XML attribute, can do this way provided that the class does not have property like the setted
      if($this->disableOnRequest){
        $this->oncomplete .= 'self.disabled=false;';
        $this->oncommand = 'this.disabled=true;'. $this->getActionHandlerScript('action', $this->oncomplete, array(), $formData);
      }else{
        $this->oncommand = $this->getActionHandlerScript('action', $this->oncomplete, $formData);
      }
    }else{
      throw new ZElementException('Formbutton should have action property.');
    }

    $this->meta->attributes['label'] = $this->label;

    $this->bindNotNullPropertyToAttribute('id');
    $this->bindNotNullPropertyToAttribute('disabled');
    $this->bindNotNullPropertyToAttribute('flex');
  }

  public function render(){
    parent::render();

    return $this->renderHead();
  }

  public function getJSValueGetter(){
    return "Zool.byId('$this->id').label";
  }

}