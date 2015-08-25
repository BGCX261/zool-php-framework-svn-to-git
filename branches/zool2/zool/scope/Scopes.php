<?php

namespace zool\scope;

use zool\base\Accessable;

use zool\management\Components;

use zool\ZException;

use Annotation\Standard\ScopeAnnotation;

use Annotation\Annotations;


class Scopes extends Accessable
{

    private $sessionScope;
    private $pageScope;
    private $requestScope;
    private $eventScope;

    private static $instance = null;

    private $controllerInstances = array();

    private function  __construct()
    {
        $this->sessionScope = SessionScope::instance();
        $this->pageScope = PageScope::instance();
        $this->requestScope = RequestScope::instance();
        $this->eventScope = EventScope::instance();
    }

    /**
     *
     * @return zool\scope\Scopes instance
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Scopes();
        }
        return self::$instance;
    }


    public function getSessionScope()
    {
        return $this->sessionScope;
    }

    public function getPageScope()
    {
        return $this->pageScope;
    }

    public function getRequestScope()
    {
        return $this->requestScope;
    }

    public function getEventScope()
    {
        return $this->eventScope;
    }

    /**
     * Scope cannot set, juset get from Scopes in the given order.
     * Precedence of Scopes :
     *     - event
     *     - request
     *     - page
     *     - conversation // TODO
     *     - session
     *     - application // TODO
     * // TODO more comment about Scopes
     */
    public function get($key, $default = null, $autoCreate = true)
    {


        $value = $this->eventScope->get($key);

        if (is_null($value))
            $value = $this->requestScope->get($key);

        if (is_null($value))
            $value = $this->pageScope->get($key);

        if (is_null($value))
            $value = $this->sessionScope->get($key);

        if (is_null($value) && $autoCreate)
            $value = Components::getInstance($key);

        if (!is_null($value)) {
            //....
            return $value;
        }

        return $default;
    }

    /**
     *
     * Return the constant represents the Scope.
     * @param $key
     */
    public function getVariableScope($key)
    {

        if (!is_null($this->eventScope->get($key)))
            return EVENT_SCOPE;

        if (!is_null($this->requestScope->get($key)))
            return REQUEST_SCOPE;

        if (!is_null($this->pageScope->get($key)))
            return PAGE_SCOPE;

        if (!is_null($this->sessionScope->get($key)))
            return REQUEST_SCOPE;

        return UNSPECIFIED_SCOPE;
    }

    public function setToScope($scope, $key, $value)
    {
        switch ($scope) {
            case EVENT_SCOPE:
                $this->eventScope->set($key, $value);
                break;
            case PAGE_SCOPE:
                $this->pageScope->set($key, $value);
                break;
            case SESSION_SCOPE:
                $this->sessionScope->set($key, $value);
                break;
            case REQUEST_SCOPE:
                $this->requestScope->set($key, $value);
                break;

            default:
                //throw new ZoolException('Cannot set variable to Scope '.$Scope);
                break;
        }
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @return Ambigous <unknown, NULL, \zool\management\ComponentProxy, object, mixed>
     */
    public function resolveFormContext($key, $default = null)
    {
        return $this->get($key, $default, true);
    }

}