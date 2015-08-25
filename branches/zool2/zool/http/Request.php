<?php

namespace zool\http;

use zool\Zool;

use zool\base\Accessable;

use zool\ComponentException;

/**
 * Featured request object. Use it only staticly.
 *
 * @author lzsolt
 *
 */
class Request extends Accessable{

    private static $instance = null;

    private static $viewIdParamName = 'viewId';

    /**
     * @return Request instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new Request;
        }
        return self::$instance;
    }

    /**
     *
     * @var array
     */
    private $delegate = null;

    private function __construct(){
        $this->delegate = $_REQUEST;
        self::$viewIdParamName =  Zool::app()->getConfiguration()->zool->request->get('viewIdParamName', self::$viewIdParamName);
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null){
        return array_key_exists($key, $this->delegate) ? $this->delegate[$key] : $default;
    }

    public function set($key, $valiue){
        $previousValue = self::get($key);

        $this->delegate[$key] = $value;

        return $previousValue;
    }


    /**
     * @return string
     */
    public function getViewId(){
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $this->get(self::$viewIdParamName);
    }

}
