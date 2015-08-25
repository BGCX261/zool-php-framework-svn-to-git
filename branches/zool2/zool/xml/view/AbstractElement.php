<?php

namespace zool\xml\view;

use zool\xml\XmlKey;

use zool\xml\element\XmlElement;

use zool\context\PageContext;
use zool\ZPropertyException;
use zool\zx\ZMethodExpression;
use Annotation\Annotations;
use zool\xml\elements\ZXmlElement;
use zool\base\ZComponent;

/**
 *
 * @author Zsolt Lengyel
 *
 */
abstract class AbstractElement extends XmlElement{

    const METHOD_PROPERTY = 1;
    const BINDING_PROPERTY = 2;
    const SIMPLE_PROPERTY = 3;

    /**
     *
     * @param XmlElemement $parent
     * @param string $tagName (e.g. ns:taname)
     * @param array $attributes
     * @param array $children
     */
    public function __construct($parent, $namespace, $attributes, $children){

        $elem = [
            XmlKey::TAG_NAME_KEY => get_class($this),
            XmlKey::TAG_ATTRIBUTES_KEY => $attributes,
            XmlKey::TAG_CHILDREN_KEY => $children
        ];

        parent::__construct($elem, $parent);

        $class = strtolower(get_class($this));

        $this->meta->name = substr($class, strrpos($class, '\\')+1);
        $this->meta->namespace = $namespace;

        /*
         * Object will has attributes as property
        */
        foreach($this->meta->attributes as $key => $value){

            // TODO
//             $annotations = Annotations::ofProperty($this, $key);

//             $propertyType = self::SIMPLE_PROPERTY;
//             foreach ($annotations as $annotation){
//                 if(get_class($annotation) == 'Annotation\\Standard\\MethodAnnotation'){
//                     $propertyType = self::METHOD_PROPERTY;
//                     break;
//                 }
//                 if(get_class($annotation) == 'Annotation\\Standard\\BindingAnnotation'){
//                     $propertyType = self::BINDING_PROPERTY;
//                     break;
//                 }
//             }

            $propertyType = self::SIMPLE_PROPERTY;

            switch($propertyType){

                case self::METHOD_PROPERTY:
                    $val = $this->evaluateZXAsMethod($value, $this);
                    break;

                case self::BINDING_PROPERTY:
                    $val = $this->bindValue($value,$this);
                    break;

                case self::SIMPLE_PROPERTY:
                default:
                    $val = $this->evaluateZX($value, $this);
                    break;
            }

            $this->$key = $val;

            unset($val);
        }

        $this->meta->attributes = array();

        $this->init();
    }

    public function __set($property, $value){
        $this->meta->attributes[$property] = $value;
    }

    protected function init(){
        $this->preAssembleChildren();
    }

    protected function beforeAssemble(){
    }

    protected function bindNotNullProperties(/* Argument names */){
        foreach (func_get_args() as $arg){
            $this->bindNotNullPropertyToAttribute($arg);
        }
    }

    protected function bindNotNullPropertyToAttribute($property, $attribute = null){
        if($attribute == null) $attribute = $property;
        if($this->$property != null){
            $this->meta->attributes[$attribute] = $this->$property;
        }
    }

    protected function bindPropertyToAttribute($property, $attribute = null){
        if($attribute == null) $attribute = $property;
        $this->meta->attributes[$attribute] = $this->$property;
    }

    public function assemble(){
        $this->beforeAssemble();
        return '';
    }

    public function appendChild($child){
        $this->meta->children[] = $child;
    }

    public function prependChild($child){
        $newChildren = array($child);
        $this->meta->children = array_merge($newChildren, $this->meta->children);
    }

    // TODO
    private function evaluateZXAsMethod($oldvalue, $context){
        if(($expression = ZMethodExpression::parse($oldvalue, $context)) !== false){
            return $expression;
        }else{
            throw new ZPropertyException("$oldvalue is not an  method expression.");
        }
    }

    // TODO
    private function bindValue($value, $context){
        return $value;
    }


}