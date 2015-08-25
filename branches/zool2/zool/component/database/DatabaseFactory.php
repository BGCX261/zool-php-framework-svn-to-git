<?php


namespace zool\component\database;

use zool\component\Component;

use zool\Zool;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Tools\Setup;

use Doctrine\ORM\Tools\SchemaTool;


/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.databaseFactory");
 *
 */
class DatabaseFactory extends Component{

    /**
     * @var EntityManager entity manager
     */
    private static $entityManager = null;

    /**
     *
     * @var SchemaTool schema tool
     */
    private static $schemaTool = null;


    /** @Factory("zool.database.entityManager") */
    public function entityManager(){
        if(self::$entityManager === null){

            $config = Setup::createAnnotationMetadataConfiguration(array(APP_PATH.'/model'), DEBUG);

            $connectionDescriptor = Zool::app()->getConfig()->get('db')->asArray();

            self::$entityManager = EntityManager::create($connectionDescriptor, $config);
        }
        return self::$entityManager;
    }

    /**
     * @var SchemaTool schema tool
     *
     * @Factory("zool.database.schemaTool")
     */
    public function schemaTool(){
        if(self::$schemaTool === null){
            self::$schemaTool = new SchemaTool(self::entityManager());
        }
        return self::$schemaTool;
        //     $classes = array(
        //             $em->getClassMetadata('Entities\User'),
        //             $em->getClassMetadata('Entities\Profile')
        //     );
        //     $tool->createSchema($classes);

    }

}