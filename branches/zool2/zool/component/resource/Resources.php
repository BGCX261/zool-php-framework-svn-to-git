<?php

namespace zool\component\resource;

use zool\file\resource\ResourceManager;

use zool\component\Component;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.resource.resources")
 */
class Resources extends Component implements \ArrayAccess{

    /**
     * @return string public URL of resource
     */
    public function get($resource){
        return ResourceManager::instance()->getResourceUrl($resource);
    }


    public function offsetExists($resource) {
        return $this->get($resource) != null;
    }

    public function offsetUnset($offset) {
        // DO NOT UNSET
    }

    public function offsetGet($resource) {
        return $this->get($resource);
    }

    public function offsetSet($resource, $content) {
        //return $this->get($resource);
        // TODO something
    }

}