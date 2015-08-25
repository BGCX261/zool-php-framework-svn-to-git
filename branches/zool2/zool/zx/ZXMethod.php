<?php

namespace zool\zx;

use zool\scope\Scopes;

use zool\controller\ZController;

use zool\context\Contexts;

use zool\Zool;

use zool\tools\ZUniqueIdGenerator;

use zool\zx\ZExpression;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class ZXMethod{

    const METHOD_EXPRESSION_PATTERN = '/\\s*#{(.*)->([a-zA-Z_][_a-zA-Z0-9]*)\\((.*)\\)}\\s*/';

    private $object;
    private $method;
    private $params;
    private $context;
    private $contextRootPath;

    private $id;

    public function __construct($object, $method, $params, $context){
        $this->object = $object;
        $this->method = $method;
        $this->params = $params;

        $class = get_class($context);
        $ns = substr($class, 0, strpos($class,'\\'));

        $this->contextRootPath = array($ns, Zool::getRootPath($ns));
    }

    public function run(){
        // TODO better method handlin
        $object = Scopes::instance()->get($this->object);
        if($object != null){
            if($object instanceof ZController){
                $object->beforeAction();
            }
            return call_user_func_array(array($object, $this->method), $this->params);
        }
        else{
            throw new ZExpressionException($this->object. ' cannot resolved from any context.');
        }
    }

    public static function parse($expr, $context = null)
    {
        $expr = trim($expr);

        if (empty($expr) || !is_string($expr)) {
            return false;
        }

        preg_match(self::METHOD_EXPRESSION_PATTERN, $expr, $matches);
        if (empty($matches)) {
            return false;
        }

        $object = $matches[1];
        $method = $matches[2];

        $paramString = trim($matches[3]);
        $str  = strlen($paramString);
        if(strlen($str)>0){
            $params = ZExpression::parseList($paramString, $context, true);
        }else $params = array();

        return new ZMethodExpression($object, $method, $params, $context);

    }

    public function getId(){
        if(!isset($this->id)){
            $this->id = 'method_'.md5($this->method.$this->object. print_r($this->params, true));
        }
        return $this->id;
    }

    public function getContextRootPath(){
        return $this->contextRootPath;
    }

}