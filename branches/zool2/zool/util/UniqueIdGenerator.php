<?php

namespace zool\util;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class UniqueIdGenerator{

    use Singleton;

    private $id = 0;

    public  $prefix = "zid";

    public static function next(){
        return $this->prefix . $this->id++;
    }

}
