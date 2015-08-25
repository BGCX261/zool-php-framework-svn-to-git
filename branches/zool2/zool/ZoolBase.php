<?php

namespace zool;

use zool\deploy\Deployment;

use zool\application\Application;

use zool\base\Accessable;

use zool\exception\ZoolException;

use zool\exception\UnimplementedMethodException;

use zool\application\ZApplication;

/**
 *
 * @author Zsolt Lengyel
 *
 */
abstract class ZoolBase
{

    protected static $app = null;
    protected static $paths = array();
    protected static $classToPath = array();

    public static function init()
    {
        // vendors
        self::$paths = [
            'Doctrine' => ZOOL_PATH . '/doctrine/Doctrine',
            'Annotation' => ZOOL_PATH . '/annotation/Annotation',
        ];

        $modules = Deployment::instance()->modules;
        foreach ($modules as $moduleName => $moduleDefinition){

            self::import($moduleDefinition['path'], $moduleName);

        }

        spl_autoload_register('zool\Zool::loadClass');

    }

    public static function loadClass($classToLoad)
    {

        if (is_file($classToLoad)) {
            require_once $classToLoad;
            return;
        }

        $class = str_replace('\\', '/', $classToLoad);
        $ns = substr($class, 0, strrpos($class, '\\'));

        $root = substr($class, 0, strpos($class, '/'));
        $classPath = str_replace($root . '/', '', $class);

        if (!array_key_exists($root, self::$paths)) {
            // throw new ZException("Unregistred namespace: '$root' at loading $classToLoad.");
            return;
        }

        $fullPath = self::$paths[$root] . "/$classPath.php";

        if (!file_exists($fullPath)) {
            throw new ZoolException("Unable to load class: $class");
        }

        require_once $fullPath;

        self::$classToPath[$classToLoad] = $fullPath;
    }

    public static function import($path, $ns = '')
    {
        if (!in_array($path, self::$paths)) {
            self::$paths[$ns] = $path;
        }
    }

    public static function getRootPath($root)
    {
        return self::$paths[$root];
    }

    public static function getClassPath($class)
    {
        if (array_key_exists($class, self::$classToPath)) {
            return self::$classToPath[$class];
        } else {
            self::loadClass($class);
            return self::$classToPath[$class];
        }
    }

    /**
     *
     * @throws ZoolException if no application initialized
     * @return Application instance
     */
    public static function app()
    {
        if (self::$app == null) {
            throw new ZoolException('No application instace.');
        }
        return self::$app;
    }

    public static function isActiveApplication(){
        return self::$app !== null;
    }

    public static function setApplication($appliaction)
    {
        if (self::$app != null) {
            throw new ZoolException('Application ahs been already set.');
        }
        self::$app = $appliaction;
    }

    // TODO
    public static function createConsoleApplication($config = null)
    {
        throw new UnimplementedMethodException(__METHOD__);
        //return self::createApplication('ConsoleApplication', $config);
    }

    /**
     * Creates an application of the specified class.
     * @param string $class the application class name
     * @param mixed $config application configuration. This parameter will be passed as the parameter
     * to the constructor of the application class.
     * @return zool\application\Application the application instance
     */
    public static function createApplication($config = null, $class = 'zool\application\Application')
    {
        return new $class($config);
    }

    public static function log($str)
    {
        foreach (explode("\n", $str) as $line)
            $log .= date("H:i:s", time()) . " " . $line . "\n";

        file_put_contents(BASE_PATH . "/log.log", $log, FILE_APPEND);
    }

    public static function debug()
    {
        if (defined('DEBUG') && DEBUG) {
            var_dump(func_get_args());
        }
    }

    public static function trace($str)
    {
        if (defined('TRACE') && TRACE) {
            echo 'Zool trace: ' . $str . "<br />\n";
        }
    }

    public static function dump($a1)
    {
        ob_start();
        var_dump($a1);
        return ob_get_clean();
    }

    public static function objectHash(&$object)
    {
        // TODO

//         if ($object instanceof \Serializable) {
//             return $object->oid;
//         }

        if (!isset($object->__oid__)) {
            ob_start();
            var_dump($object, microtime());
            $stamp = ob_get_clean();
            $object->__oid__ = sha1(spl_object_hash($object) . $stamp);
        }
        return $object->__oid__;
    }

}
