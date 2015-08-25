<?php

namespace zool\event;

use zool\management\Components;

use zool\deploy\Deployment;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Events{

    private static $instance = null;

    /**
     *
     * @return zool\event\Events isntance of Events class
     */
    public static function  instance(){
        if(null == self::$instance){
            self::$instance = new Events();
        }

        return self::$instance;
    }

    private $events = [];

    /**
     * Private constructor.
     */
    private function __construct(){
        $this->events = Deployment::instance()['events'];
    }

    /**
     * Adds a listener to an event
     *
     * @param string $event key of event
     * @param mixed $callable callable variable
     */
    public function addEventListener($event, $callable){
        $this->events[$event] = $callable;
    }

    /**
     * Fires the specified event. The listeners will called by the given priority at Observer annotation.
     *
     * @param string $event name of event
     * @param array $parameters event parameters
     */
    public function raise($event){

        $parameters = [];

        // called like this: raise('event', param, param1, param2, ...)
        if(func_num_args() > 1){
            $event = func_get_arg(0);

            $tmpParameters = func_get_args();
            $parameters = array_splice($tmpParameters, 1);
        }

        if(isset($this->events[$event])){

            $listeners = $this->events[$event];

            foreach ($listeners as $listener){

                if($listener['static']){

                    Components::callStaticMethod($listener['component'],  $listener['method'], $parameters);

                }else{

                    self::callNonStaticListener($listener, $parameters);

                }

            }

        }

    }

    /**
     * Call non-static component method.
     *
     * @param array $listener listener definition
     */
    private static function  callNonStaticListener($listener, $parameters){
        if(isset($listener['component'])){

            $component = Components::getInstance($listener['component']);

        }else if(isset($listener['class'])){

            $className = $listener['class'];
            $component = new $className;

        }

        $callable = [$component, $listener['method']];

        call_user_func_array($callable, $parameters);
    }


}