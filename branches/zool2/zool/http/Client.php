<?php

namespace zool\http;

use zool\base\ztrait\Singleton;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Client{

    use Singleton;

    public function getClientType(){
        $agent = $_SERVER['HTTP_USER_AGENT'];

        if(strpos($agent, 'Chrome') !== false)
            return 'chrome';

        if(strpos($agent, 'Mozilla/5.0') !== false){
            if(strpos($agent, 'Firefox') !== false){
                return 'firefox';
            }else{
                return 'xulrunner';
            }
        }else return 'other';

    }

    public function isFirefox(){
        return $this->getClientType() == 'firefox';
    }

    public function isXulRunner(){
        return $this->getClientType() == 'xulrunner';
    }

    public function isChrome(){
        return $this->getClientType() == 'chrome';
    }

}