<?php

namespace zool\xml\view;


use zool\xml\element\XmlRootElement;

use zool\scope\Scopes;

use zool\base\Accessable;

/**
 *
 * @author Zsolt Lengyel
 *
 */
abstract class AbstractDocument extends Accessable{

    /**
     *
     * @var array document tree
     */
    protected $document = [];

    protected $header = '';

    protected $documentContext = [];

    protected $contexts;

    protected $context = [];

    protected $viewProvider;

    public function __construct(array $doc, AbstractViewProvider $viewProvider){

        $this->viewProvider = $viewProvider;
        $this->document = $doc;

        $this->header = $this->document[0];

        if(empty($this->document)){
            return;
        }

        $this->contexts = Scopes::instance();

        $this->init();
    }

    public function init(){
    }

    public function assemble(){
        // o like output

        $rootELement = $this->createRootElement();

        if(!is_null($rootELement)){
            return [
            $this->header,
            $rootELement->assemble()
            ];
        }
        return $this->heaser;
    }

    public function getViewProvider(){
        return $this->viewProvider;
    }


    /**
     *
     * @return \zool\xml\view\XmlRootElement|NULL
     */
    protected function createRootElement(){
        $tree = $this->getDocumentTree();
        if(!empty($tree))
            return new XmlRootElement($this->getDocumentTree(), $this);
        else return null;
    }

    public function getDocumentTree(){
        return $this->document[1];
    }

    public function setContext($key, $value){
        $this->context[$key] = $value;
    }

    public function getContext($key, $default = null){
        if(array_key_exists($key, $this->context)){
            return $this->context[$key];
        }
        return $default;
    }

    public function resolveFromContext($name, $default = null)
    {
        $value = $this->getContext($name);
        if(is_null($value)){
            $value = Scopes::instance()->get($name, $default);
        }
        return (is_null($value) ? $default : $value);
    }

    public function getParent(){
        return null;
    }

}