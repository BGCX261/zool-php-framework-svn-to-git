<?php

namespace zool\base;

use zool\base\module\Module;

use zool\http\HttpException;

use zool\event\Events;

use \zool\Zool;

abstract class BaseApplication extends Module
{
    /**
     * @var string the application name. Defaults to 'My Application'.
     */
    public $name='Zool Application';
    /**
     * @var string the charset currently used for the application. Defaults to 'UTF-8'.
     */
    public $charset='UTF-8';
    /**
     * @var string the language that the application is written in. This mainly refers to
     * the language that the messages and view files are in. Defaults to 'en_us' (US English).
     */
    public $sourceLanguage='en_us';

    private $_id;
    private $_basePath;
    private $_runtimePath;
    private $_extensionPath;
    private $_globalState;
    private $_stateChanged;
    private $_ended=false;
    private $_language;
    private $_homeUrl;


    /**
     * Constructor.
     * @param mixed $config application configuration.
     * If a string, it is treated as the path of the file that contains the configuration;
     * If an array, it is the actual configuration information.
     * Please make sure you specify the {@link getBasePath basePath} property in the configuration,
     * which should point to the directory containing all application logic, template and data.
     * If not, the directory will be defaulted to 'protected'.
     */
    public function __construct($name, $config)
    {
        parent::__construct($name, $config);
        Zool::setApplication($this);

        $this->initSystemHandlers();

    }


    /**
     * Runs the application.
     * This method loads static application components. Derived classes usually overrides this
     * method to do more application-specific tasks.
     * Remember to call the parent implementation so that static application components are loaded.
     */
    public function run()
    {

    }

    /**
     * Terminates the application.
     * This method replaces PHP's exit() function by calling
     * {@link onEndRequest} before exiting.
     * @param integer $status exit status (value 0 means normal exit while other values mean abnormal exit).
     * @param boolean $exit whether to exit the current request. This parameter has been available since version 1.1.5.
     * It defaults to true, meaning the PHP's exit() function will be called at the end of this method.
     */
    public function end($status=0, $exit=true)
    {
        Events::instance()->raise('zool.endRequest', $status);

        if($exit)
            exit($status);
    }

    /**
     * Returns the language that the user is using and the application should be targeted to.
     * @return string the language that the user is using and the application should be targeted to.
     * Defaults to the {@link sourceLanguage source language}.
     */
    public function getLanguage()
    {
        return $this->_language===null ? $this->sourceLanguage : $this->_language;
    }

    /**
     * Specifies which language the application is targeted to.
     *
     * This is the language that the application displays to end users.
     * If set null, it uses the {@link sourceLanguage source language}.
     *
     * Unless your application needs to support multiple languages, you should always
     * set this language to null to maximize the application's performance.
     * @param string $language the user language (e.g. 'en_US', 'zh_CN').
     * If it is null, the {@link sourceLanguage} will be used.
     */
    public function setLanguage($language)
    {
        $this->_language=$language;
    }

    /**
     * Returns the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_get().
     * @return string the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }

    /**
     * Sets the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_set().
     * @param string $value the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-set.php
     */
    public function setTimeZone($value)
    {
        date_default_timezone_set($value);
    }


    /**
     * Handles uncaught PHP exceptions.
     *
     * @param Exception $exception exception that is not caught
     */
    public function handleException($exception)
    {
        // disable error capturing to avoid recursive errors
        restore_error_handler();
        restore_exception_handler();

        $category= get_class($exception);

        if($exception instanceof HttpException){
            $category.='.'.$exception->getStatusCode();
            http_response_code($exception->getStatusCode());
        }
        // php <5.2 doesn't support string conversion auto-magically
        $message=$exception->__toString();
        if(isset($_SERVER['REQUEST_URI']))
            $message.="\nREQUEST_URI=".$_SERVER['REQUEST_URI'];
        if(isset($_SERVER['HTTP_REFERER']))
            $message.="\nHTTP_REFERER=".$_SERVER['HTTP_REFERER'];
        $message.="\n---";

        $this->displayException($exception);

        try
        {
            Events::instance()->raise('zool.exeption', $this, $exception);
        }
        catch(Exception $e)
        {
            $this->displayException($e);
        }

        try
        {
            $this->end(1);
        }
        catch(Exception $e)
        {
            // use the most primitive way to log error
            $msg = get_class($e).': '.$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n";
            $msg .= $e->getTraceAsString()."\n";
            $msg .= "Previous exception:\n";
            $msg .= get_class($exception).': '.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().")\n";
            $msg .= $exception->getTraceAsString()."\n";
            $msg .= '$_SERVER='.var_export($_SERVER,true);
            error_log($msg);
            exit(1);
        }
    }

    /**
     * Handles PHP execution errors such as warnings, notices.
     *
     * This method is implemented as a PHP error handler. It requires
     * that constant ENABLE_ERROR_HANDLER be defined true.
     *
     * This method will first raise an {@link onError} event.
     * If the error is not handled by any event handler, it will call
     * {@link getErrorHandler errorHandler} to process the error.
     *
     * The application will be terminated by this method.
     *
     * @param integer $code the level of the error raised
     * @param string $message the error message
     * @param string $file the filename that the error was raised in
     * @param integer $line the line number the error was raised at
     */
    public function handleError($code,$message,$file,$line)
    {
        if($code & error_reporting())
        {
            // disable error capturing to avoid recursive errors
            restore_error_handler();
            restore_exception_handler();

            $log="$message ($file:$line)\nStack trace:\n";
            $trace=debug_backtrace();
            // skip the first 3 stacks as they do not tell the error position
            if(count($trace)>3)
                $trace=array_slice($trace,3);
            foreach($trace as $i=>$t)
            {
                if(!isset($t['file']))
                    $t['file']='unknown';
                if(!isset($t['line']))
                    $t['line']=0;
                if(!isset($t['function']))
                    $t['function']='unknown';
                $log.="#$i {$t['file']}({$t['line']}): ";
                if(isset($t['object']) && is_object($t['object']))
                    $log.=get_class($t['object']).'->';
                $log.="{$t['function']}()\n";
            }
            if(isset($_SERVER['REQUEST_URI']))
                $log.='REQUEST_URI='.$_SERVER['REQUEST_URI'];

            $this->displayError($code, $message, $file, $line);

            try{
                Events::instance()->raise('zool.error', $this,$code,$message,$file,$line);
            }
            catch(Exception $e){
                $this->displayException($e);
            }

            try
            {
                $this->end(1);
            }
            catch(Exception $e)
            {
                // use the most primitive way to log error
                $msg = get_class($e).': '.$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n";
                $msg .= $e->getTraceAsString()."\n";
                $msg .= "Previous error:\n";
                $msg .= $log."\n";
                $msg .= '$_SERVER='.var_export($_SERVER,true);
                error_log($msg);
                exit(1);
            }
        }
    }


    /**
     * Displays the captured PHP error.
     * This method displays the error in HTML when there is
     * no active error handler.
     * @param integer $code error code
     * @param string $message error message
     * @param string $file error file
     * @param string $line error line
     */
    public function displayError($code,$message,$file,$line)
    {
        if(DEBUG)
        {
            echo "<h1>PHP Error [$code]</h1>\n";
            echo "<p>$message ($file:$line)</p>\n";
            echo '<pre>';

            $trace=debug_backtrace();
            // skip the first 3 stacks as they do not tell the error position
            if(count($trace)>3)
                $trace=array_slice($trace,3);
            foreach($trace as $i=>$t)
            {
                if(!isset($t['file']))
                    $t['file']='unknown';
                if(!isset($t['line']))
                    $t['line']=0;
                if(!isset($t['function']))
                    $t['function']='unknown';
                echo "#$i {$t['file']}({$t['line']}): ";
                if(isset($t['object']) && is_object($t['object']))
                    echo get_class($t['object']).'->';
                echo "{$t['function']}()\n";
            }

            echo '</pre>';
        }
        else
        {
            echo "<h1>PHP Error [$code]</h1>\n";
            echo "<p>$message</p>\n";
        }
    }

    /**
     * Displays the uncaught PHP exception.
     * This method displays the exception in HTML when there is
     * no active error handler.
     * @param Exception $exception the uncaught exception
     */
    public function displayException($exception)
    {
        if(DEBUG)
        {
            echo '<h3>'.get_class($exception)."</h3>\n";
            echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
            echo '<pre>'.$exception->getTraceAsString().'</pre>';

            if($exception->getPrevious() !== null){
                $this->displayException($exception->getPrevious());
            }
        }
        else
        {
            echo '<h1>'.get_class($exception)."</h1>\n";
            echo '<p>'.$exception->getMessage().'</p>';
        }
    }

    /**
     * Initializes the class autoloader and error handlers.
     */
    protected function initSystemHandlers()
    {
        if(ENABLE_EXCEPTION_HANDLER)
            set_exception_handler(array($this,'handleException'));
        if(ENABLE_ERROR_HANDLER)
            set_error_handler(array($this,'handleError'),error_reporting());
    }

}
