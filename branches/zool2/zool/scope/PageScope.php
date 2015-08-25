<?php

namespace zool\scope;

use zool\http\Request;

use zool\Scope;
use zool\Zool;


class PageScope implements Scope
{

    const CONTEXT_NAME = 'page';
    const PREVIOUS_PAGE_NAME = 'zool_previous_page';
    const CURRENT_PAGE_NAME = 'zool_current_page';
    const CONTEXT_CLASSES_NAME = 'page_classes';
    const CONTEXT_ROOT_PATHS_NAME = 'page_rootpaths';
    const CLASS_NAME_SEPARATOR = ':';

    private $context = [];

    private $classesNeedToLoad;
    private $rootPaths;

    private $currentPage;
    private $previousPage;

    private static $instance = null;

    private function  __construct()
    {
        //$this->reset();

        if (!isset($_SESSION[ZOOL_CONTEXT_NAME][self::PREVIOUS_PAGE_NAME])) {
            $_SESSION[ZOOL_CONTEXT_NAME][self::PREVIOUS_PAGE_NAME] = $this->previousPage = $this->currentPage = Request::instance()->getViewId();
        } else {
            $this->previousPage = $_SESSION[ZOOL_CONTEXT_NAME][self::PREVIOUS_PAGE_NAME];
            $this->currentPage = isset($_SESSION[ZOOL_CONTEXT_NAME][self::CURRENT_PAGE_NAME]) ? $_SESSION[ZOOL_CONTEXT_NAME][self::CURRENT_PAGE_NAME] : $this->previousPage;
        }

        /*
         * Persist previous page
        */
        if (!isset($this->currentPage) || $this->currentPage != $this->previousPage) {
            $this->previousPage = isset($_SESSION[ZOOL_CONTEXT_NAME][self::CURRENT_PAGE_NAME]) ? $_SESSION[ZOOL_CONTEXT_NAME][self::CURRENT_PAGE_NAME] : Request::instance()->getViewId();
            $_SESSION[ZOOL_CONTEXT_NAME][self::CURRENT_PAGE_NAME] = $this->currentPage = Request::instance()->getViewId();
            $this->reset();
        }

        /*
         * Reset context, if other page
        */
        if (!isset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME]) || $this->currentPage != $this->previousPage) {
            $this->reset();
        }


        if (isset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME])) {
            $this->classesNeedToLoad = $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME];
        } else {
            $this->classesNeedToLoad = [];
        }

        if (isset($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_ROOT_PATHS_NAME])) {
            $this->rootPaths = $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_ROOT_PATHS_NAME];
        } else {
            $this->rootPaths = [];
        }

        foreach ($this->classesNeedToLoad as $class) {
            Zool::loadClass($class);
        }

        foreach ($this->rootPaths as $ns => $path) {
            Zool::import($path, $ns);
        }

        foreach ($_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME] as $key => $value) {
            $this->context[$key] = unserialize($value);
        }

    }

    /**
     * Saving out the session.
     */
    public function __destruct()
    {
        foreach ($this->context as $key => $value) {
            $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME][$key] = serialize($value);
        }
        $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME] = $this->classesNeedToLoad;
        $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_ROOT_PATHS_NAME] = $this->rootPaths;
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new PageScope();
        }
        return self::$instance;
    }

    public function set($key, $value)
    {
        if (is_object($value)) {
            // TODO recursive class check
            $class = get_class($value);
            $this->addToClasses($class);
        }
        $this->context[$key] = $value;
    }

    public function deset($key)
    {
        unset($this->context[$key]);
    }

    public function get($key, $default = null)
    {
        if (isset($this->context[$key])) {
            return $this->context[$key];
        }

        return $default;
    }

    public function isSetted($key)
    {
        return isset($this->context[$key]);
    }

    public static function className()
    {
        return __CLASS__;
    }

    public function addToClasses($class)
    {
        if (!in_array($class, $this->classesNeedToLoad)) {
            $this->classesNeedToLoad[] = $class;
        }
    }

    public function addToRootPaths($ns, $path)
    {
        $this->rootPaths[$ns] = $path;
    }

    public function reset()
    {
        $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_NAME] = [];
        $_SESSION[ZOOL_CONTEXT_NAME][self::CONTEXT_CLASSES_NAME] = [];
        $this->context = [];
    }

}