<?php

namespace zool\util\log;

use zool\file\File;

use zool\util\Strings;

use zool\base\Accessable;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class Log extends Accessable{

    const DEFAULT_FORMAT = " {date(H:i:s)} {severity}\t[{scope}]\t\t{content}";

    public static $CONSOLE_FORMAT = "{date(m-d H:i:s)} {severity}\t[{scope}]\t\t{content}";

    const INFO = 'INFO';

    const DEBUG = 'DEBUG';

    const TRACE = 'TRACE';

    const WARNING = 'WARNING';

    const ERROR = 'ERROR';

    const FATAL = 'FATAL';

    const CONSOLE = 'CONSOLE';


    private $scope;

    private $format;

    private $datePlace;

    private $dateFormat = 'H:m:s';

    private $file;

    /**
     *
     * @var File file
     */
    private $fileHandler;


    /**
     *
     * @param string $scope scope of log
     * @param string $format format of output
     */
    public function __construct($scope = '', $file = null, $format = null){
        $this->scope = $scope;

        $this->parseFormat($format);

        $this->file = $file;

        if(!Strings::isEmpty($this->file)){
            $this->fileHandler = new File($this->file . '_'.date('Y-m-d').'.log');
            $this->fileHandler->open('a');
        }
    }

    private function parseFormat($format){

        $this->format = $format;
        Strings::setIfEmpty($this->format, self::DEFAULT_FORMAT);

        preg_match('/\\{date(\\((.*)\\))?\\}/', $this->format, $dateMatches);

        $this->datePlace = $dateMatches[0];
        if(isset($dateMatches[2])){
            $this->dateFormat = $dateMatches[2];
        }
    }

    public function __destruct(){
        if($this->fileHandler !== null)
            $this->fileHandler->close();
    }

    public function log($content, $severity = self::INFO, $args = null){
        $log = $this->format;

        $log = str_replace('{scope}', $this->scope, $log);
        $log = str_replace($this->datePlace, date($this->dateFormat), $log);
        $log = str_replace('{severity}',$severity, $log);
        $log = str_replace('{content}', $content, $log);

        if($args !== null){
            $log .= "\n".var_export($args, true);
        }

        $this->writeToOutPut($log);
    }

    private function writeToOutPut($line){

        if($this->fileHandler === null){
            echo $line .PHP_EOL;
        }else{
            $this->fileHandler->writeLine($line);
        }

    }

    public function info($content, $args = null){
        $this->log($content, self::INFO, $args);
    }

    public function debug($content, $args = null){
        if(defined('DEBUG') && DEBUG)
            $this->log($content, self::INFO, $args);
    }

    public function trace($content, $args = null){
        $this->log($content, self::TRACE, $args);
    }

    public function error($content, $args = null){
        $this->log($content, self::ERROR, $args);
    }

    public function fatal($content, $args = null){
        $this->log($content, self::FATAL, $args);
    }

    public function console($content, $args = null, $severity = self::INFO){
        if(!isset($_SERVER['HTTP_USER_AGENT'])){
            $prevFormat = $this->format;

            $this->parseFormat(self::$CONSOLE_FORMAT);
            $this->log($content, $severity, $args);

            $this->parseFormat($prevFormat);
        }
    }

}