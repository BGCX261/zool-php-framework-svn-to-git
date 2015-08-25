<?php

namespace zool\i18n;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class MessageFormat{

    /**
     * @var MessageFormat singleton instance
     */
    private static $instance;

    /**
     * @return MessageFormat instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new MessageFormat;
        }
        return self::$instance;
    }

    /**
     * Singleton constructor.
     */
    private function __construct(){
    }

    /**
     *
     * @param string $format
     * @param unknown_type $args
     * @return mixed
     */
    public function format($format, $args){

        // support parameter list instead of array of parameters
        if(!(func_num_args() == 2 && is_array(func_get_arg(1)))){
            $format = func_get_arg(0);
            $args = func_get_args();
            $args = array_slice($args, 1);
        }

        $formattedMessage = $format;

        for ($i = 0; $i < count($args); $i++){

            $place = '{'.$i.'}';
            $replacement = $args[$i];

            if(is_object($replacement)){
                $replacement = $replacement->__toString();
            }

            $formattedMessage = str_replace($place, $replacement, $formattedMessage);
        }

        return $formattedMessage;

    }

}