<?php

namespace zool\aspects\xul;
use zool\xml\ZXmlParser;

use zool\aspects\ZAspect;

class ZXulAspect extends ZAspect{

  public function init(){
    header("Content-Type: application/vnd.mozilla.xul+xml; charset=utf-8");
  }

  public function run(){
    $doc = new ZXulDocument($this->currentViewId, $this->baseDocuemt,$this);
    return $doc->render();
  }

  public function setErrorHandler(){
    set_error_handler(array($this, 'errorHandler'));
    set_exception_handler(array($this, 'exceptionHandler'));
  }

  public function createDocument($file){
    $doc  = ZXmlParser::fromFileToTree($file);
    return new ZXulDocument($file, $doc, $this);
  }

  public function createDocumentFromTree($viewId, $tree){
    return new ZXulDocument($viewId, $tree, $this);
  }

  public function getLibraryPath(){
    return dirname(__FILE__);
  }

  public function getName(){
    return 'xul';
  }

  public function errorHandler($errno, $errstr, $errfile, $errline){
   echo '<?xml version="1.0"?>';
    echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
    ?>
     <window
        id="window"
        title="Zool excpetion"
        orient="horizontal"
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

        <vbox style="font-size:16px;padding:10px;">

            <hbox style="font-size:30px; color:darkred;">
            Error happend
            </hbox>
            <vbox style="border:1px gray dashed;padding:4px;">
              <hbox>
                  <description>[<?php echo $errno;?>] <description style="font-weight:bold;"><?php echo $errstr;?></description></description>
              </hbox>
              <hbox>
                <spacer flex="1" />
                <box>
                  in <description style="font-weight:bold;"><?php echo $errfile;?></description> on line <description style="font-weight:bold;"><?php echo $errline; ?></description>
                </box>
              </hbox>
            </vbox>
        </vbox>

   </window>

    <?php
    die();
  }

  public function exceptionHandler($e){
    echo '<?xml version="1.0"?>';
    echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
    ?>
     <window
        id="window"
        title="Zool excpetion"
        orient="horizontal"
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

        <vbox style="font-size:16px;padding:10px;">

            <hbox style="font-size:30px; color:darkred;">
            Uncaught exception
            </hbox>
            <vbox style="border:1px gray dashed;padding:4px;">
              <hbox>
                  <description>[<?php echo get_class($e);?>] <description style="font-weight:bold;"><?php echo $e->getMessage();?></description></description>
              </hbox>
              <hbox>
                <spacer flex="1" />
                <box>
                  in <description style="font-weight:bold;"><?php echo $e->getFile();?></description> on line <description style="font-weight:bold;"><?php echo $e->getLine(); ?></description>
                </box>
              </hbox>
            </vbox>
        </vbox>

   </window>

    <?php
  }

}