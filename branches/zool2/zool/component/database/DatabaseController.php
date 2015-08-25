<?php

namespace zool\component\database;

use zool\deploy\Deployment;

use zool\component\Component;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.database.databaseController")
 */
class DatabaseController extends Component{


    /**
     *
     * @var EntityManager
     *
     * @In(value="zool.database.entityManager", required=true)
     */
    public $entityManager;

    /**
     *
     * @var SchemaTool
     * @In(value="zool.database.schemaTool",required=true)
     */
    public $schemaTool;


    public function createSchema(){
        $metas = [];

        foreach (Deployment::instance()->models as $model){
            $metas[] = $this->entityManager->getClassMetadata($model);
        }

        $this->schemaTool->createSchema($metas);
    }

}