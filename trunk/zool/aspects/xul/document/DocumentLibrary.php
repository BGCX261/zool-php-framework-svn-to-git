<?php

namespace xul\document;

use zool\aspects\ZLibrary;

class DocumentLibrary extends ZLibrary{

  public function getPath(){
    return dirname(__FILE__);
  }

}