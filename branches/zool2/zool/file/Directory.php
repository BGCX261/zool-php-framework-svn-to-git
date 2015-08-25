<?php

namespace zool\file;

use zool\exception\UnimplementedMethodException;

/**
 * File system direcotry.
 *
 * @author Zsolt Lengyel
 *
 */
class Directory{

    /**
     * Normalizes to the OS the path.
     *
     * @param string $path normalized path. Slash (/) separated path.
     * @return string
     */
    public static function normalize($path){

        /*
         * Removing separator duplications.
        */
        while(false !== strstr($path, '//')){
            $path = str_replace('//', '/', $path);
        }

        // on Win
        if(DIRECTORY_SEPARATOR == '\\'){
            return str_replace('/', '\\', $path);
        }

        return realpath($path);

    }

    /**
     *
     * @param string $path to check
     * @return boolean true if directory exists
     */
    public static function isDir($path){
        $path = self::normalize($path);
        return (new Directory($path))->exists();
    }

    /**
     *
     * @var string path
     */
    private $path;

    /**
     * @param string path of directory
     */
    public function __construct($path){
        $this->path = Directory::normalize($path);
    }

    /**
     * @see \zool\file\File::exists()
     * @return true if the path is a existing directory
     */
    public function exists(){
        return is_dir($this->path);
    }

    /**
     * Creates directory.
     */
    public function create(){
        if(!$this->exists()){
            mkdir($this->path, '0777', true);
        }
    }

    /**
     *
     * @param boolean $recursively true if we want all the file from subtrees too
     * @param string $regexpFilter a regular expression for filter the file name
     */
    public function getFiles($recursively = false, $regexpFilter = null){
        $result = [];

        if($recursively === true){

            $result = $this->getFilesRecursively($regexpFilter);

        }else{

            $dirIterator = new \DirectoryIterator($this->path);
            if(!empty($regexpFilter)){
                $dirIterator = new \RegexIterator($dirIterator, $regexpFilter, \RecursiveRegexIterator::GET_MATCH);
            }

            foreach ($dirIterator as $key => $file){
                $result[] = $this->getPath().DS.$file[0];
            }
        }

        return $result;

    }

    public function getDirectories($recursively = false){

        if($recursively === true){
            throw new UnimplementedMethodException();
        }else{

            return glob($this->path .'/*' , GLOB_ONLYDIR);
        }

    }

    /**
     * @param string $regexpFilter a regular expression for filter the file name
     */
    protected function getFilesRecursively($regexpFilter = null){

        $directory = new \RecursiveDirectoryIterator($this->path);
        $iterator = new \RecursiveIteratorIterator($directory);

        if(!empty($regexpFilter)){
            $iterator = new \RegexIterator($iterator, $regexpFilter, \RecursiveRegexIterator::GET_MATCH);
        }

        $result = [];

        foreach ($iterator as $key => $file){
            $result[] = $file[0];
        }

        return $result;

    }

    /**
     *
     * @return string path of dir
     */
    public function getPath(){
        return $this->path;
    }



}