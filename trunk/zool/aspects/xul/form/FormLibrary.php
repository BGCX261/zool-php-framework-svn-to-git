<?php

namespace xul\form;

use zool\aspects\ZLibrary;

class FormLibrary extends ZLibrary{

    public function getPath(){
        return dirname(__FILE__);
    }

}