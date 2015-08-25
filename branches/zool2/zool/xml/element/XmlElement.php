<?php

namespace zool\xml\element;

use zool\xml\XmlKey;

use zool\base\Accessable;

use zool\aspects\ZDocument;

use zool\ZException;

use zool\Zool;
use zool\xml\Xml;
use zool\base\ZComponent;
use zool\zx\ZExpression;

class XmlElement extends Accessable{

    protected $meta = null;

    protected $assembledChildren = [];

    protected $_context = [];

    public function __construct($elem, $parent)
    {

        $this->meta = new XmlMeta;

        $parsedName = $this->parseName($elem[XmlKey::TAG_NAME_KEY]);

        $this->meta->baseElem = $elem;
        $this->meta->namespace = $parsedName[0];
        $this->meta->name = $parsedName[1];
        $this->meta->parent = $parent;

        if(isset($elem[XmlKey::TAG_ATTRIBUTES_KEY]))
            $this->meta->attributes = $elem[XmlKey::TAG_ATTRIBUTES_KEY];

        if(isset($elem[XmlKey::TAG_CHILDREN_KEY]))
        {
            $this->meta->children = $elem[XmlKey::TAG_CHILDREN_KEY] ;
        }

        if (is_array($this->meta->attributes)){

            foreach ($this->meta->attributes as $key => $value) {
                if (substr($key, 0, 5) == 'xmlns') {
                    $namespace = '';
                    if (substr($key, 5, 1) == ':') {
                        $ns = explode(':', $key, 2);
                        $namespace = $ns[1];
                    }
                    $this->meta->namespaces[$namespace] = $value;

                    $this->parseNamespace($namespace, $value);

                    unset($this->meta->attributes[$key]);
                }
            }
        }

    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getDocument()
    {
        $parent = $this->meta->parent;
        while(!($parent instanceof Document)){
            $parent = $parent->meta->parent;
        }
        return $parent;
    }

    public function getParent(){
        return $this->meta->parent;
    }

    public function getChildren(){
        return $this->meta->children;
    }


    protected function parseName($name)
    {

        if ($pos = strpos($name, ':')) {
            $tmp = explode(':', $name);
            return array($tmp[0], $tmp[1]);
        }

        return array('', $name);

    }

    protected function parseNamespace($namespace, $url)
    {
        $parsed = parse_url($url);

        if ($parsed['scheme'] == 'lib') {
            Zool::app()->viewProvider->registerLibrary($namespace, $url);
            unset($this->meta->namespaces[$namespace]);
        }
    }

    public function setContext($name, $value)
    {
        $this->_context[$name] = $value;
    }

    public function getContext($name, $default = null)
    {
        if (array_key_exists($name, $this->_context)) {
            return $this->_context[$name];
        }
        return $default;
    }

    public function unsetContext($name){
        unset($this->_context[$name]);
    }

    public function resolveFromContext($name, $default = null)
    {

        $val = $this->getContext($name);
        if (is_null($val)) {
            $val = $this->getParent()->resolveFromContext($name, $default);
        }
        return (is_null($val) ? $default : $val);
    }

    public function assemble()
    {

        $result = [];

        if ($lib = $this->getViewProvider()->getLibrary($this->meta->namespace)) {

            $elem = $lib->createElement($this->meta->parent, $this->getFullName(), $this->meta->attributes, $this->meta->children);

            // handling new element
            if($elem !== null){

                // renders the library tag
                $result = $elem->assemble();

                // setting the parent new children
                if (!$this->isRootElement()) {

                }

            }else{
                $this->preAssembleChildren();
                $result = $this->assembleSimple();
            }

        } else {

            $this->preAssembleChildren();
            $result = $this->assembleSimple();
        }


        return $result;

    }



    protected function renderHead($complex = false)
    {

        $attributes = $this->renderAttributes();

        $o = '<' . $this->getFullName() . (!empty($attributes) ? ' ' . $attributes : '');
        if (count($this->meta->children) > 0 && !$complex) {
            $o .= ">";
        } else {
            $o .= " />\n";
        }

        return $o;
    }

    protected function getHead(){
        $attributes = $this->renderAttributes();
        return '<' . $this->getFullName() . (!empty($attributes) ? ' ' . $attributes : ''). '>';
    }

    protected function tagPrefix()
    {
        return ($this->meta->namespace == '' ? '' : $this->meta->namespace . ':');
    }

    protected function renderFoot()
    {
        if (count($this->meta->children) > 0) {
            return $this->getFoot();
        }
        return '';
    }

    protected function getFoot(){
        return '</' . $this->getFullName() . ">\n";
    }

    protected function getPreAssembledChildren()
    {
        if (is_array($this->meta->children) && !empty($this->meta->children) && count($this->assembledChildren) != count($this->meta->children)) {
            throw new XmlElementException('Children have not prerendered yet for '.$this->meta->name);
        }
        return $this->assembledChildren;
    }

    /**
     *
     */
    protected function assembleSimple()
    {
        return $this->compose($this->getFullName(), $this->meta->attributes, $this->getAssembledChildren());
    }

    /**
     *
     */
    protected function compose($name, $attributes, $children){
        return [
        XmlKey::TAG_NAME_KEY => $name,
        XmlKey::TAG_ATTRIBUTES_KEY => $attributes,
        XmlKey::TAG_CHILDREN_KEY => $children
        ];
    }

    protected function preAssembleChildren()
    {
        // must reset before filling
        $this->assembledChildren = [];
        if(is_array($this->children))
            foreach ($this->children as $child) {
            if(is_string($child)){
                $this->assembledChildren[] = $this->evaluateZX($child);
            }else{
                $childElem = new XmlElement($child, $this);
                $this->assembledChildren[] = $childElem->assemble();

            }

        }

    }

    protected function getAssembledChildren(){
        return $this->assembledChildren;
    }

    protected function renderAttributes()
    {
        $o = array();

        foreach ($this->meta->namespaces as $key => $value) {
            $o[] = "xmlns" . ($key == '' ? '' : ':') . "$key=\"$value\"";
        }

        if (!empty($this->meta->attributes) && is_array($this->meta->attributes)){
            foreach ($this->meta->attributes as $key => $value) {

                $value = $this->evaluateZX($value);

                $o[] = "$key=\"$value\"";
            }
        }
        return join(" ", $o);
    }

    public function getFullName()
    {
        return $this->tagPrefix() . $this->meta->name;
    }

    protected function evaluateZX($oldvalue)
    {
        $value = $oldvalue;
        if (($expr = ZExpression::parse($value, $this)) !== false) {
            try {
                $value = $expr->getValue();

                // its just a nested ZX
                if($oldvalue != $expr->fullExpression){
                    $value = str_replace($expr->fullExpression, $value, $oldvalue);
                }
            }
            catch (ZExpressionException $e) {
                throw new ZException("Failed to evaluate ZX in $this->fullName. ZX: {$expr->expression}",
                201, $e);
            }
        }

        return $value;
    }

    public function isRootElement()
    {
        return $this instanceof ZXmlRootElement;
    }

    /**
     *
     * @return \zool\xml\view\AbstractViewProvider
     */
    public function getViewProvider(){
        return Zool::app()->viewProvider;
    }


    public function __toString(){
        return $this->fullName;
    }

}