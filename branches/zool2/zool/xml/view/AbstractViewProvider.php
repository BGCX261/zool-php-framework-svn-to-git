<?php

namespace  zool\xml\view;

use zool\xml\view\ElementLibrary;

use zool\xml\XmlParser;

use zool\xml\view\AbstractEnvelope;

use zool\xml\view\AbstarctEnvelope;

use zool\base\Accessable;

use zool\Zool;

use zool\context\RequestContext;

use zool\base\ZComponent;

/**
 *
 * Enter description here ...
 * @author Zsolt Lengyel
 *
 */
abstract class AbstractViewProvider extends Accessable{


    /**
     *
     * @var AbstractEnvelope
     */
    private $envelope;

    /**
     *
     * @var array
     */
    protected $baseDocuemt;

    /**
     *
     * @var array namespace => ElementLibrary
     */
    protected $libraries = [];

    protected $currentViewId;

    /**
     *
     * @var XmlElement elementthat is under rendering
     */
    protected $document = null;

    public abstract function handleReRender();

    protected abstract function createEnvelope();

    public abstract function createDocument($tree, $viewProvider);

    /**
     *
     * @param string $viewFile path of view file
    */
    public final function __construct($viewAlias){
        $this->envelope = $this->createEnvelope();

        $this->currentViewId = $viewAlias;
        $doc  = XmlParser::instance()->fromFileToTree($viewAlias);
        $this->baseDocuemt = $doc;

        $this->init();
    }

    /**
     * @return array
     */
    public function assemble(){
        $doc = $this->createDocument($this->baseDocuemt,$this);

        return $doc->assemble();
    }

    protected function init(){
    }

    /**
     *
     * @return \zool\xml\view\AbstractEnvelope
     */
    public function getEnvelope(){
        return $this->envelope;
    }

    /**
     *
     * @return array
     */
    protected function getBaseDocument(){
        return $this->baseDocuemt;
    }

    /**
     *
     * @return \zool\xml\view\XmlElement
     */
    public function getDocument(){
        return $this->document;
    }

    public function registerLibrary($inNamespace, $url){

        return ($this->libraries[$inNamespace] = new ElementLibrary($inNamespace, $url));
    }

    /**
     * @var \zool\xml\view\ElementLibrary
     */
    public function getLibrary($namespace){
        if(array_key_exists($namespace, $this->libraries)){
            return $this->libraries[$namespace];
        }
        return false;
    }

    public function getLibraryNamespace($library){
        foreach ($this->libraries as $ns => $lib) {

            if($lib->library == $library){
                return $ns;
            }
        }

        return null;

    }

    public function createTemplateDocument($file){
        $doc  = ZXmlParser::fromFileToTree($file);
        return new TemplateDocument($file, $doc, $this);
    }




}

