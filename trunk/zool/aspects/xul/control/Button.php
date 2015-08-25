<?php

namespace xul\control;

use zool\context\SessionContext;

use zool\context\PageContext;

use zool\aspects\ZElement;

use \zool\Zool;

class Button extends ZElement{

  public $label = '';
  public $id = null;
  public $disableOnRequest = true;
  public $oncomplete = null;
  public $disabled = null;
  public $flex = null;
  public $rendered = true;
  public $dir;
  public $orient;
  public $image;
  public $tooltip;
  public $tooltiptext;

  /**
   * @Method
   */
  public $action;
  public $reRender = '';

  public function init(){

    if(!$this->rendered) return;

    $this->meta->name = 'button';

    if($this->action != null){
      // If you want to set an XML attribute, can do this way provided that the class does not have property like the setted
      if($this->disableOnRequest){
        $this->oncomplete .= 'self.disabled=false;';
        $this->oncommand = 'this.disabled=true;'. $this->getActionHandlerScript('action', $this->oncomplete);
      }else{
        $this->oncommand = $this->getActionHandlerScript('action', $this->oncomplete);
      }
    }elseif($this->oncomplete !== null){
      $this->meta->attributes['oncommand'] = $this->oncomplete;
    }

    $this->meta->attributes['label'] = $this->label;

    $this->bindNotNullPropertyToAttribute('id');
    $this->bindNotNullPropertyToAttribute('disabled');
    $this->bindNotNullPropertyToAttribute('flex');
    $this->bindNotNullPropertyToAttribute('orient');
    $this->bindNotNullPropertyToAttribute('dir');
    $this->bindNotNullPropertyToAttribute('image');
    $this->bindNotNullPropertyToAttribute('tooltip');
    $this->bindNotNullPropertyToAttribute('tooltiptext');
  }

  public function render(){
    parent::render();

    if(!$this->rendered) return '';

    return $this->renderHead(). $this->getChildrenOutput().$this->renderFoot();
  }
}
