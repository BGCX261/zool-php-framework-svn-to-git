<?php

namespace zool\file\resource;

use zool\file\PathResolver;

use zool\base\ztrait\Singleton;

use zool\web\UrlManager;

use zool\http\Request;

use Doctrine\Common\Annotations\Annotation\Target;

use zool\file\Directory;

use zool\util\HashGenerator;

use zool\base\module\Modules;

use zool\util\Strings;

use zool\file\File;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class ResourceManager{

    use Singleton;


    const RESOURCE_DIRNAME = 'resource';

    public function  getResourceUrl($alias){

        list($moduleName, $path) = PathResolver::instance()->moduleNameAndPath($alias);

        $modulePath = Modules::instance()->pathOf($moduleName);

        $resourceFile = new File($modulePath .'/'. self::RESOURCE_DIRNAME .'/'.$path);

        if(!$resourceFile->exists()){
            $this->throwResourceNotFoundException($resourcePath);
        }

        $resourceLocation = $this->copyToResources($resourceFile, $resourcePath)[1];

        return UrlManager::instance()->geBaseUrl() .'/'. $resourceLocation;
    }

    /**
     *
     * @param zool\file\File $resourceFile
     * @return File
     */
    private function copyToResources($resourceFile, $resourcePath){

        $baseName = $resourceFile->getFileNameWithoutExtension();
        $extension = $resourceFile->getExtension();

        $fileHash = HashGenerator::instance()->hash($resourceFile->getMTime(), 7);

        $hashedBaseName = $baseName.'_'.$fileHash;

        $hashedResourcePath = str_replace($baseName, $hashedBaseName, $resourcePath);

        $targetPath = RESOURCES_PATH.'/'.$hashedResourcePath;

        $targetFile = new File($targetPath);
        if(!$targetFile->exists()){

            /*
             * Delete previous versions of file
            */
            $targetDir = $targetFile->getDirectory();
            if($targetDir->exists()){

                $baseName = $targetFile->getFileNameWithoutExtension();
                $prevFilesPattern = '/^'.$baseName.'_(.){7}\.'.$extension.'$/i';
                $prevoiusFiles = $targetDir->getFiles(false, $prevFilesPattern);

                foreach ($prevoiusFiles as $prevFilePath){
                    (new File($prevFilePath))->delete();
                }
            }

            $targetFile->content($resourceFile->content());

        }

        return [
        $targetFile, RESOURCES_DIRNAME.'/'.$hashedResourcePath
        ];
    }

    /**
     *
     * @throws ResourceException
     */
    private function throwResourceNotFoundException($resourcePath){
        throw new ResourceException("Could not find: $resourcePath");
    }

}