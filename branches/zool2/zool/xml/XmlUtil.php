<?php

namespace zool\xml;

/**
 * XML tools.
 *
 * @author Zsolt Lengyel
 *
 */
class XmlUtil{

    /**
     *
     * @param string $element
     * @param string $name
     * @param string $library
     * @return multitype:|Ambigous <multitype:unknown , multitype:>
     */
    public static function getElementsByName($element, $name, $library){

        if(!is_array($element)) return []; // perhaps CDATA

        $result = array();

        $searchedElement = self::getTagFullName($library, $name);

        if(is_array($element) && $element[ZXml::TAG_NAME_KEY] == $searchedElement){
            $result[] = &$element;
        }

        if(array_key_exists(ZXml::TAG_CHILDREN_KEY, $element)){
            foreach ($element[ZXml::TAG_CHILDREN_KEY] as $child){
                $result = array_merge($result, self::getElementsByName($child, $name, $library));
            }
        }
        return $result;

    }

    /**
     *
     * @param array $tree on we search
     * @param string $id ID of element
     * @return NULL|array founded element
     */
    public static function getElementById($tree, $id){

        if(!is_array($tree)) return null;

        $attributes = array_key_exists(ZXml::TAG_ATTRIBUTES_KEY, $tree) ? $tree[ZXml::TAG_ATTRIBUTES_KEY] : [];

        if(array_key_exists('id', $attributes) && $attributes['id'] == $id){
            return $tree;
        }

        $element = null;
        $children = array_key_exists(ZXml::TAG_CHILDREN_KEY, $tree) ? $tree[ZXml::TAG_CHILDREN_KEY] : [];

        foreach ($children as $child) {
            if(( $founded = self::getElementById($child, $id)) !== null){
                return $founded;
                break;
            }
        }

        return $element;
    }

    /**
     *
     * @param array $xml xml descriptor array
     * @param boolean $forceOpenTag
     * @return string renderd tree/tag
     */
    public static function renderArray($xml, $forceOpenTag = false){

        $o = '<'.$xml[0]. (isset($xml[1]) && is_array($xml[1]) ? self::renderAttributes($xml[1]): '');

        if($forceOpenTag || (isset($xml[2]) && is_array($xml[2]) && count($xml[2]>0))){
            $o .= ">\n";

            if(!$forceOpenTag)
                foreach ($xml[2] as $child){
                $o .= self::renderArray($child);
            }

            if(!$forceOpenTag)
                $o .= "\n</".$xml[0].">\n";
        }else{
            $o .= '/>';
        }
        return $o;
    }

    /**
     *
     * @param string $name
     * @param array $attributes attributes
     * @param array $children children
     * @param array $forceOpenTag not-empty tags
     * @return string rendered tag
     */
    public static function renderTag($name, $attributes = [], $children = [], $forceOpenTag = false){

        $o =  '<'.$name.self::renderAttributes($attributes) . (empty($children) && !$forceOpenTag ? '/>' : '>');

        if(is_array($children))
            $o .= implode("\n", $children);
        else
            $o .= $children;

        if(!empty($children) || $forceOpenTag) $o .= '</'.$name.'>';

        return $o . "\n";
    }

    /**
     *
     */
    public static function renderTree($tree){

        if(!is_array($tree)){
            return "$tree";
        }

        $o = '';

        $children = $tree[XmlKey::TAG_CHILDREN_KEY];


        if(isset($tree[XmlKey::TAG_NAME_KEY])){
            $o .=  '<'.$tree[XmlKey::TAG_NAME_KEY].self::renderAttributes($tree[XmlKey::TAG_ATTRIBUTES_KEY]) . (empty($children) ? '/>' : '>');


            if(is_array($children)){

                foreach ($children as $child){
                    $o .= self::renderTree($child);
                }
            }else
                $o .= $children;

            if(!empty($children)) $o .= '</'.$tree[XmlKey::TAG_NAME_KEY].'>';
        }
        return $o;

    }

    /**
     * Render attributes
     *
     * @param array $attrs atributes key: attribute name, value: attribute value
     * @render string rendered attributes
     */
    public static function renderAttributes($attrs){
        $out = [];
        foreach ($attrs as $key => $value){
            if($value != null){
                $out[] = $key.'="'.$value.'"';
            }
        }
        return empty($out) ? '' : ' '.implode(' ', $out);
    }

    /**
     *
     */
    private static function getTagFullName($library, $name){
        return (empty($library)) ? $name : $library .':'. $name;
    }

}