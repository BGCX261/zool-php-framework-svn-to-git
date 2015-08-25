<?php

namespace xul\layout;

use zool\xml\ZXmlUtil;

use zool\aspects\ZElement;

class Grid extends ZElement{

  public $columns = 1;
  public $id = null;
  public $flex = null;
  public $cellFlex = null;

  public function render(){
    parent::render();

    $o = ZXmlUtil::renderArray(array(
      0 => 'grid',
      1 => array('flex'=> $this->flex, 'id' => $this->id)
    ),true);

    $o .= '<columns>';
    for($i=0; $i<$this->columns; ++$i){
      $o .= ZXmlUtil::renderArray(array(
          0 => 'column',
          1 => array('flex'=> $this->flex)
        ));
    }
    $o .= '</columns>';

    $o .= '<rows>';

    $childNum = 0;
    foreach ($this->renderedChildren as $child){

      if(is_string($child) && trim($child) == '') continue;

      if($childNum == 0){
        $o .= '<row>';
      }

      $o .= $child;

      if(++$childNum == $this->columns){
        $o .= '</row>';
        $childNum = 0;
      }

    }

    if($childNum != 0){
      $o .= '</row>';
    }

    $o .= '</rows>';
    $o .= '</grid>';

    return $o;
  }

}