<?php

namespace xul\control;

use zool\aspects\ZLibrary;

class ControlLibrary extends ZLibrary{

    public function getPath(){
        return dirname(__FILE__);
    }

}