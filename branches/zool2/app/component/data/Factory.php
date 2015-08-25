<?php

namespace app\component\data;

use zool\component\Component;

/**
 *
 * @author Zsolt Lengyel
 *
 * @Component("app.factory");
 */
class Factory extends Component{

    /** @Factory("app.data") */
    public function data(){
        return [1,2,3,4,56];
    }

}
