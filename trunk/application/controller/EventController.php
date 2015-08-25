<?php

namespace app\controller;

use zool\context\RequestContext;

use zool\xml\ZXmlUtil;

use zool\xml\ZXmlParser;

use zool\Zool;

use app\model\Bug;

use zool\context\SessionContext;

use zool\controller\ZController;
/**
 *
 * Demo controller
 * @author dev
 *
 * @scope(EVENT_SCOPE)
 */
class EventController extends ZController{

  public function dom(){

    RequestContext::instance()->set('foo', true);
    RequestContext::instance()->set('bar', rand(1, 20));

    $colors = array('blue', 'red', 'orange', 'green', 'gray', 'yellow');
    $c = $colors[array_rand($colors, 1)];
    RequestContext::instance()->set('color', $c);

    \shuffle($colors);

    SessionContext::instance()->set('colors', $colors);

    RequestContext::instance()->set('title', $c);


  }

}