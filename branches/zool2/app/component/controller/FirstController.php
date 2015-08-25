<?php

namespace app\component\controller;

use zool\util\log\Log;

use first\component\Es;

use Doctrine\ORM\Tools\ToolsException;

use zool\http\HttpException;

use zool\component\database\DatabaseController;

use zool\deploy\Deployment;

use Doctrine\ORM\Tools\SchemaTool;

use Doctrine\ORM\EntityManager;

use zool\management\ComponentException;

use zool\exception\ZoolException;

use zool\scope\Scopes;

use zool\util\log\LogProvider;

use zool\application\Application;

use zool\component\Component;

/**
 * @Component("firstController1")
 * @Scope(ScopeType::EVENT)
 */
class FirstController extends Component{

    /**
     *
     * @var
     * @In("resources")
     */
    public static $staticProp;

    public static function method(){
        echo "static";
    }


    /**
     * @var string
     *
     * @In(required=false)
     * @Out(required=false, scope=ScopeType::SESSION)
     */
    public $var;

    public $hash;

    /**
     * @var Log log
     * @Logger('paramLogger')
     */
    public $log;

    /**
     * @var Log log
     * @Logger
     */
    public $log3;


    /** @RequestParam(required=true) */
    public $id;

    /**
     * @var Es
     *
     * @In("first.es")
     **/
    public $es;

    /** @Observer('valami')
     *  @RequestParameterized
     **/
    public function run($v1, $v2, $v3=1, $v4=2){

        $this->es->sayHellop($v1.'-'.$v2);

        $this->log->info('fior');
        $this->log3->info('fior');

        $this->var = '1';

    }

    /** @Factory(value = "hashVal", scope = ScopeType::STATELESS) */
    public function hash(){
        return $this->hash;
    }
}
