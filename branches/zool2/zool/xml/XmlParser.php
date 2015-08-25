<?php

namespace zool\xml;


use zool\base\module\Modules;

use zool\util\HashGenerator;

use zool\file\PathResolver;

use zool\base\ztrait\Singleton;

use zool\file\File;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class XmlParser
{

    const XML_RUNTIME_DIRECTORY = 'xmlParser';

    const TMPFILE_EXTENSION = '.php';

    private static $TMP_DIR;

    /**
     * @var XmlParser singleton instance
     */
    private static $instance;

    /**
     * @return XmlParser instance
     */
    public static function instance(){
        if(null === self::$instance){
            self::$instance = new XmlParser;
        }
        return self::$instance;
    }

    /**
     * Singleton constructor.
     */
    private function __construct(){
        self::$TMP_DIR = RUNTIME_PATH.DS.self::XML_RUNTIME_DIRECTORY;
    }

    /**
     * @see PathResolver
     * @param string $file file alias
     * @throws ZException when file not found
     * @return Ambigous <multitype:, multitype:NULL string , multitype:unknown string >
     */
    public function fromFileToTree($alias)
    {

        $filePath = PathResolver::instance()->resolve($alias);

        $file = new File($filePath);

        if(!$file->exists()){
            throw new XmlException("Cannot open file $file.");
        }

        $tmpFilePath = $this->getTmpFilePath($alias, $file);
        $tmpFile = new File($tmpFilePath);


        /*
         * Catching
        */
        if($tmpFile->exists()){

            return (require $tmpFile->getPath());

        }else{

            /*
             *  Remove old file
            */

            $targetDir = $tmpFile->getDirectory();
            if($targetDir->exists()){
                $baseName = $file->getFileNameWithoutExtension();
                $extension = $file->getExtension();

                $prevFilesPattern = '/^'.$baseName.'_(.){7}\.'.$extension.'$/i';
                $prevoiusFiles = $targetDir->getFiles(false, $prevFilesPattern);

                foreach ($prevoiusFiles as $prevFilePath){
                    (new File($prevFilePath))->delete();
                }
            }


            $doc =  self::toTree($file->content());

            /*
             * Generates an interpretable code.
            */
            $exported = '<?php return ' .var_export($doc, true) . ";\n";

            $tmpFile->content($exported);

            return $doc;

        }
    }

    private function getTmpFilePath($alias, $resourceFile){
        list($module, $path) = PathResolver::instance()->moduleNameAndPath($alias);

        $modulePath = Modules::instance()->pathOf($module);

        $dirAndFile = str_replace($modulePath, '', $resourceFile->getPath());
        $dir = str_replace($resourceFile->getFileName(), '', $dirAndFile);

        $baseName = $resourceFile->getFileNameWithoutExtension();
        $extension = $resourceFile->getExtension();

        $fileHash = HashGenerator::instance()->hash($resourceFile->getMTime(), 7);
        $hashedFileName = $baseName.'_'.$fileHash .'.'.$extension;

        return self::$TMP_DIR .'/'.$module .'/'. $dir .'/'. $hashedFileName;
    }

    /**
     *
     * @param string $contents XML file component
     * @return array XML hierarchy tree
     */
    public static function toTree($contents)
    {
        $contents = trim($contents);

        if (!function_exists('xml_parser_create')) {
            // TODO use another parser instead
            return array();
        }

        $parser = xml_parser_create('');

        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
        xml_parse_into_struct($parser, $contents, $xml_values, $indexes);
        xml_parser_free($parser);

        $current = &$xml_values[0];
        $nodes = array();

        // if true, must handle another way the result
        $completeRoot = false;


        foreach($xml_values as $key => &$elem){

            $elem[XmlKey::TAG_NAME_KEY] = $elem['tag'];
            unset($elem['tag']);

            if(isset($elem['attributes'])){
                $elem[XmlKey::TAG_ATTRIBUTES_KEY] = $elem['attributes'];
                unset($elem['attributes']);
            }

            // No root, no document
            if(!array_key_exists('level', $elem)){
                return array(0=>$contents, 1=>null);
            }

            $level = $elem['level'];
            $type = XmlKey::typeToInt($elem['type']);
            unset($elem['type']);

            switch($type){
                case XmlKey::OPEN_TYPE:
                    $elem[XmlKey::TAG_CHILDREN_KEY] = array();

                    if(isset($elem['value'])){
                        $value = $elem['value'];
                        $elem[XmlKey::TAG_CHILDREN_KEY] = array(0 => $value);
                    }
                    unset($elem['value']);

                    $nodes[$level-1] = &$elem;
                    $current = &$elem;

                    break;

                case XmlKey::CDATA_TYPE:
                    if($value = trim($elem['value']) != ''){
                        $elem['tag'] = 'CDATA';
                        $current[XmlKey::TAG_CHILDREN_KEY][] = &$elem;
                    }
                    break;

                case XmlKey::COMPLETE_TYPE:
                    if(isset($elem['value'])){
                        $value = $elem['value'];
                        $elem[XmlKey::TAG_CHILDREN_KEY] = array(0 => $value);
                    }
                    unset($elem['value']);

                    if(empty($nodes)){
                        $completeRoot = true;
                        $root = &$elem;
                    }else{
                        $current[XmlKey::TAG_CHILDREN_KEY][] = &$elem;
                    }

                    break;

                case XmlKey::CLOSE_TYPE:
                    $nodes[$level - 2][XmlKey::TAG_CHILDREN_KEY][] = &$current;
                    $current = &$nodes[$level - 2];
                    unset($nodes[$level-1]);
                    break;
            }

            unset($elem['level']);
            unset($elem);

            if($completeRoot)break;
        }

        if(!$completeRoot){
            $root = $current[XmlKey::TAG_CHILDREN_KEY][0];
        }

        $header = substr($contents, 0, strpos($contents, '<'.$root[0]));

        return array(0=>$header, 1=>$root);

    }


}
