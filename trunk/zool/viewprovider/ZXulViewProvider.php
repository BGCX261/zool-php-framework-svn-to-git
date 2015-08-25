<?php

namespace zool\viewprovider;

use zool\xml\ZXmlUtil;

use zool\xml\ZXmlParser;

use zool\aspects\xul\ZXulAspect;

use zool\Zool;

class ZXulViewProvider extends ZViewProvider{

  private $envelope;

  public function init(){
    $this->setJSONErrorHandlers();
    $this->envelope = new XulJsonEnvelope();
    $request = Zool::app()->request;
    $this->aspect= new ZXulAspect($request);
  }

  public function render(){
    parent::render();

    header('Content-type: application/vnd.mozilla.xul+xml');
    $view = $this->aspect->run();

    echo $view;
  }

  public function handleReRender(){

    $reRender = isset($_REQUEST['reRender']) ? $_REQUEST['reRender'] : false;

    $jsonResult = array();

    if($reRender){

      $toReRender = explode(',', $reRender);
      $toReRender = array_map('trim', $toReRender);

      foreach ($toReRender as $rendering){

        list($viewId, $id) = array_map('trim', explode(':', $rendering));

        $doc = ZXmlParser::fromFileToTree(APP_PATH.$viewId);

        $element = ZXmlUtil::getElementById($doc[1], $id);

        $wrapper = array(0=>'',
        1=>array(
        0=> 'z:fragment',
        1=> array(
        // TODO get valid libs
                'xmlns:z'=>'lib://core',
              	'xmlns:c'=>'lib://control',
              	'xmlns:f'=>'lib://form',
				'xmlns:l'=>'lib://layout',
              	),
              	2=> array($element)
              	)
              	);

              	$docToRender = $this->aspect->createDocumentFromTree($viewId, $wrapper);
              	$rendered = $docToRender->render();

              	$xmlTree = ZXmlParser::toTree($rendered);
              	$jsonResult[$id] = $xmlTree[1];
      }

      $this->envelope->reRender = $jsonResult;

      echo $this->envelope->toJSON();
    }
  }

  public function getEnvelope(){
    return $this->envelope;
  }

  private function setJSONErrorHandlers(){
    set_error_handler(array($this, 'jsonErrorHandler'));
    set_exception_handler(array($this, 'jsonExceptionHandler'));
  }

  public function jsonErrorHandler($errno, $errstr, $errfile, $errline){
    $envelope = new XulJsonEnvelope();
    $envelope->error = "Error happened:\n".
    "[$errno] $errstr\n at $errfile on line $errline";

    die($envelope->toJSON());
  }

  public function jsonExceptionHandler($e){
     $envelope = new XulJsonEnvelope();
    $envelope->error = "Uncaught exception:\n".
    "[".get_class($e)."] " .$e->getMessage() . "\n at ".$e->getFile()." on line ".$e->getLine();

    die($envelope->toJSON());
  }

  public function getClientType(){
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if(strpos($agent, 'Mozilla/5.0') !== false){
      if(strpos($agent, 'Firefox') !== false){
        return 'firefox';
      }else{
        return 'xulrunner';
      }
    }else return 'other';

  }

}

class XulJsonEnvelope{

  public $reRender;
  public $error  = false;
  public $message = '';

  public function toJSON(){
    $this->setHeader();

    $response = array(
      'error'=>$this->error,
      'reRender'=>$this->reRender,
      'message'=>$this->message
    );

    return json_encode($response);
  }

  public function setHeader(){
    header("Content-Type: application/json; charset=utf-8");
  }

}