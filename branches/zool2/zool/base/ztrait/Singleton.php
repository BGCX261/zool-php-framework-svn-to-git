<?php

namespace zool\base\ztrait;

/**
 *
 * @author Zsolt Lengyel
 *
 */
trait Singleton
{
    private static $instance;

    /**
     *
     * @return class of
     */
    final public static function instance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    final private function __construct() {
        $this->init();
    }

    protected function init() {}
    final private function __wakeup() {}
    final private function __clone() {}
}