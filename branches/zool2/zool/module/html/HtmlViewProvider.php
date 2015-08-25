<?php

namespace html;


use zool\xml\XmlUtil;

use zool\xml\view\AbstractViewProvider;

use zool\xml\view\ViewProvider;

/**
 *
 * @author Zsolt Lengyel
 *
 */
class HtmlViewProvider extends AbstractViewProvider{


    public function assemble(){
        $tree = parent::assemble();
        return XmlUtil::renderTree($tree[1]);
    }

    public function handleReRender(){}

    protected function createEnvelope(){}

    public function createDocument($tree, $provider){
        return new HtmlDocument($tree, $provider);
    }

}
