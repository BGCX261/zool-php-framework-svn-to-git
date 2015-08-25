<?php

namespace zool;

interface IXmlElement{
    public function render();
    public function &getParent();
    public function getChildren();
    public function getDocument();
    
    public function getMeta();
    
     public function resolveFromContext($name, $default = null);
}