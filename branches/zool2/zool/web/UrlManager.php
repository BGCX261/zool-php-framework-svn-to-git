<?php

namespace zool\web;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class UrlManager{

    use Singleton;

    private $scheme;
    private $server;
    private $port;
    private $path;
    private $script;

    private $relativeUrl;
    private $absoluteUrl;

    /**
     * Singleton constructor.
     */
    protected function init(){

        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->server = $_SERVER['SERVER_NAME'];
        $this->port = $_SERVER['SERVER_PORT'];
        $this->script = $_SERVER['SCRIPT_NAME'];
        $this->path = substr($this->script, 0, strrpos($this->script, '/'));

        $this->relativeUrl = $this->path;
        $this->absoluteUrl = $this->scheme .'://'. $this->server . ($this->port == 80 ? '' : ':'.$this->port) . $this->path;
    }

    public function geBaseUrl($absolute = true){
        return $absolute ? $this->absoluteUrl : $this->relativeUrl;
    }
}