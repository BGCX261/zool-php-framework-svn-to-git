<?php

namespace zool\xml\view;

/**
 *
 * @author Zsolt Lengyel
 *
 */
abstract class AbstractEnvelope{

  public $reRender;
  public $error  = false;
  private $message = array();


  public abstract function render();

  public abstract function setHeader();

  public function setMessage($title, $content){
    $this->message = array('title'=>$title, 'content'=>$content);
  }



}