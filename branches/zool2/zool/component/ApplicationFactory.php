<?php

namespace zool\component;

use zool\file\resource\Resources;

use zool\event\Events;

use zool\util\time\Watch;

use zool\http\Request;

use zool\Zool;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("zool.applicationFactory")
 * @Scope(STATELESS_SCOPE)
 */
class ApplicationFactory extends Component{

    /** @Factory("zool.application") */
    public function application(){
        return Zool::app();
    }

    /** @Factory("zool.events") */
    public function events(){
        return Events::instance();
    }

    /** @Factory("zool.util.time.watch") */
    public function watch(){
        return new Watch();
    }
}
