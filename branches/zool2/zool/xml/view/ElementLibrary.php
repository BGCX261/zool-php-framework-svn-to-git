<?php

namespace zool\xml\view;

use zool\xml\view\AbstractElement;

use zool\file\PathResolver;

use zool\base\module\Modules;

use zool\base\module\Module;

use zool\util\Strings;

use zool\exception\ZoolException;

use zool\base\Accessable;

/**
 *
 * Support creation of AbstractElements.
 *
 * @author Zsolt Lengyel
 *
 */
class ElementLibrary extends Accessable{

    const OUT_NAMESPACE_ARGUMENT = 'ns';

    const LIBRARY_DIRECTORY_NAME = 'element';

    private $outNamespace = '';

    private $inNamespace = '';

    private $module = '';

    private $path = '';

    private $name = '';


    /**
     * Init constructor.
     */
    public function __construct($inNamespace, $url){

        $urlParts = parse_url($url);

        $this->name = $urlParts['host'];

        list($this->module, $library) = explode(PathResolver::MODULE_SEPARATOR, $this->name);

        $ibraray = str_replace('//', '\\', $library);

        $this->path = "{$this->module}\\".self::LIBRARY_DIRECTORY_NAME."\\{$library}\\";

        $this->inNamespace = $inNamespace;

        if(isset($urlParts['query']))
            $this->outNamespace = $this->outNamespace($urlParts['query']);

    }

    /**
     *
     * @param XmlElement $parent
     * @param string $name fullname
     * @param array $attributes associative array
     * @param array $children listarray
     * @return AbstractElement element instance
     */
    public function createElement($parent, $name, $attributes = [], $children = []){

        list($namespace, $tagName) = self::getNamespaceAndTagName($name);

        $elementClass = $this->path. ucfirst($tagName);

        $element = new $elementClass($parent, $namespace, $attributes, $children);

        return $element;
    }

    /**
     *
     * @param string $query query part of library URL
     * @return string out namespace
     */
    private function outNamespace($query){

        $queryParams = [$query];

        if(Strings::contains($query, '&')){
            $queryParams = explode('&', $query);
        }

        foreach ($queryParams as $param){
            list($arg, $value) = explode('=', $attribute);

            if($arg == self::OUT_NAMESPACE_ARGUMENT)
                return $value;
        }

    }

    /**
     *
     * @param string $fullTagname
     * @return array first: namespace, second: tag
     */
    public static function getNamespaceAndTagName($fullTagname){
        return explode(':', $fullTagname, 2);
    }


}