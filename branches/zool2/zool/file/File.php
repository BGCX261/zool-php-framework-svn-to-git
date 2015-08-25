<?php

namespace zool\file;

/**
 * Descriptor of a file system entry.
 *
 * @author Zsolt Lengyel
 *
 */
class File{

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

        return $path;

    }

    /**
     * @var string absolute path of the file
     */
    protected $path;

    /**
     * @var resource file resource
     */
    protected $handler = null;

    public function __construct($path){
        if($path instanceof File){
            $this->path = $path->getPath();
        }else{
            $this->path = self::normalize($path);
        }
    }

    public function content($content = null){
        if($content === null)
            return file_get_contents($this->path);
        return $this->touch($content);
    }

    public function open($mode){
        if(!$this->isOpened()){
            $this->createDir();
            $this->handler = fopen($this->path, $mode);
        }
    }

    /**
     * @return true if the the path of file exists and its a file
     */
    public function exists(){
        return file_exists($this->path) && is_file($this->path);
    }

    public function touch($content = null){
        $this->createDir();

        if($this->isOpened()){
            $this->write($content);
        }elseif(!$this->exists() && $content == null){
            touch($this->path);
        }elseif($content != null){
            file_put_contents($this->path, $content);
        }
    }

    public function createDir(){
        (new Directory(dirname($this->path)))->create();
    }

    /**
     *
     * @param string $content
     * @return int number of written bytes
     */
    public function write($content){
        return fwrite($this->handler, $content);
    }

    /**
     *
     * @param string $content to write to the next line
     * @return int number of written bytes
     */
    public function writeLine($content){
        return $this->write($content.PHP_EOL);
    }

    /**
     * Closes the file handler.
     * @return boolean true if close was successful
     */
    public function close(){
        if($this->isOpened())
            return fclose($this->handler);
        return true;
    }

    public function isOpened(){
        return is_resource($this->handler);
    }

    /**
     * Copy the content of this file to another.
     *
     * @param string $targetPath
     * @return \zool\file\File
     */
    public function copyTo($targetPath){
        $this->createDir();
        //$targetPath = self::normalize($targetPath);
        // copy($this->path, $targetPath);
        $targetFile = new File($targetPath);
        $targetFile->content($this->content());

        return $targetFile;
    }

    public function getMTime(){
        return filemtime($this->path);
    }

    public function getFileName(){
        $path = $this->path;
        return substr($path, strrpos($path, DIRECTORY_SEPARATOR)+1);
    }

    public function getFileNameWithoutExtension(){
        $fileName = $this->getFileName();
        return substr($fileName, 0, strrpos($fileName, '.'));
    }

    public function getExtension(){
        $fileName = $this->getFileName();
        return substr($fileName, strrpos($fileName, '.') +1);
    }

    /**
     *
     * @return string path of file
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @return mixed include result
     */
    public function includeFile(){
        return include $this->getPath();
    }


    /**
     * @return \zool\file\Directory
     */
    public function getDirectory(){
        return new Directory(dirname($this->path));
    }

    /**
     * Deletes this file.
     */
    public function delete(){
        unlink($this->path);
    }

    /**
     * @return string
     */
    public function __toString(){
        return $his->getPath();
    }


}