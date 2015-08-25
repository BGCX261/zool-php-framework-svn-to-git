<?php

namespace first\component;

use zool\component\Component;

/** @Component('first.es') */
class Es extends Component{

    public function sayHellop($to=1){
        echo "HEllo from es ".$to;
    }

}
