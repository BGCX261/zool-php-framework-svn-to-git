<?php

namespace zool\i18n;

use zool\deploy\Deployment;

use zool\file\File;

use zool\util\Strings;

use zool\Zool;

/**
 * Internationalization and localization implemetation.
 *
 * @author Zsolt Lengyel
 *
 */
class Messages{

    const MESSAGE_PATH_SEPARATOR = '.';

    const LOCALE_DIRNAME = 'locale';

    const LOCALE_FILE_EXTENSION = '.php';

    /**
     * @var Messages singleton instance
     */
    private static $instance;

    /**
     * @return Messages instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new Messages;
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
     * @var array the localized messages
     */
    private $messages = [];

    private $loadedFileBases = [];

    /**
     *
     * @param string $key message key
     * @return string messages
     */
    public function get($key){

        $fileKey = Strings::splitBy($key, self::MESSAGE_PATH_SEPARATOR);

        if($fileKey[0] == $key){
            throw new MessageException("Key must separated with ".self::MESSAGE_PATH_SEPARATOR. " in $key");
        }

        $fileBase = $fileKey[0];
        $messageKey = $fileKey[1];

        $lang = Zool::app()->getLanguage();

        if(!isset($this->messsages[$lang][$fileBase]))
            $this->loadLocale($lang, $fileBase, $messageKey);


        if(isset($this->messsages[$lang][$fileBase][$messageKey])){

            return $this->messsages[$lang][$fileBase][$messageKey];

        }elseif(Strings::contains($messageKey, self::MESSAGE_PATH_SEPARATOR)){
            // find module locale
            $tmp = Strings::splitBy($messageKey, self::MESSAGE_PATH_SEPARATOR);
            $fileBase .= self::MESSAGE_PATH_SEPARATOR . $tmp[0];
            $messageKey = $tmp[1];
        }

        if(isset($this->messsages[$lang][$fileBase][$messageKey])){
            return $this->messsages[$lang][$fileBase][$messageKey];
        }

        return  $key;

    }


    /**
     * Loads locale file if exists.
     */
    private function loadLocale($lang, $fileBase, $messageKey){

        if(!isset($this->messages[$lang])){
            $this->messages[$lang] = [];
        }

        // load from file
        if(!isset($this->messsages[$lang][$fileBase])){

            $localSuffix = '/'.self::LOCALE_DIRNAME.'/'. $lang .'/'.$fileBase.self::LOCALE_FILE_EXTENSION;

            $localeFile = new File(APP_PATH.$localSuffix);

            if($localeFile->exists()){

                // when the module not
                $this->messsages[$lang][$fileBase] = require_once ($localeFile->getPath());



            }

            if(!isset($this->messsages[$lang][$fileBase][$messageKey])){
                foreach (Deployment::instance()->modules as $moduleName => $moduleDef){
                    if($moduleName == $fileBase){

                        $moduleFileBase = Strings::splitBy($messageKey, self::MESSAGE_PATH_SEPARATOR)[0];

                        $this->tryToLoadLocaleFormModule($lang, $moduleName, $moduleDef['path'], $moduleFileBase);

                    }
                }
            }

            // could not loaded from anywhere
            if(!isset($this->messsages[$lang][$fileBase]))
                $this->messsages[$lang][$fileBase] = [];

        }

        $this->loadedFileBases[] = $fileBase;
    }

    private function tryToLoadLocaleFormModule($lang, $module, $path, $fileBase){
        if(in_array( $module.self::MESSAGE_PATH_SEPARATOR.$fileBase, $this->loadedFileBases)){
            return;
        }
        // set to flag do not load again
        $this->loadedFileBases[] = $module.self::MESSAGE_PATH_SEPARATOR.$fileBase;

        $localSuffix = '/'.self::LOCALE_DIRNAME.'/'. $lang .'/'.$fileBase.self::LOCALE_FILE_EXTENSION;

        $localeFile = new File($path.$localSuffix);

        if($localeFile->exists()){

            $this->messsages[$lang][$module.self::MESSAGE_PATH_SEPARATOR.$fileBase] = require_once ($localeFile->getPath());

        }



    }

}