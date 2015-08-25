<?php

namespace app\controller;

use zool\context\PageContext;

use zool\context\RequestContext;

use zool\Zool;

use app\model\Bug;

use zool\context\SessionContext;

use zool\controller\ZController;
/**
 *
 * Demo controller
 * @author dev
 *
 * @scope(SESSION_SCOPE)
 */
class HelloController extends ZController{

  /** @Out('scope'=>SESSION_SCOPE) */
  public $list;

  private $count = 1;

  private $string = 20;

  public function form(){

    $rc =  PageContext::instance();

    $t1 = $rc->get('textboxvalue1');
    $t1 = $t1 != null ? $t1 .'_|' : 'foo' ;

    $rc->set('textboxvalue1', $t1);

  }

  public function sayHello($name = 'Johnson', $prefix = 'Pie',$boo = '4'){

    $em = Zool::app()->em;

    if($this->count > 10) $this->count = 1;

    $this->list = $em->createQuery('select b from app\model\Bug b where b.id > 1')->setFirstResult(0)->setMaxResults(15)
    ->getResult();

    PageContext::instance()->set('list', $this->list);
    RequestContext::instance()->set('listcount', count($this->list));

    SessionContext::instance()->set('count', $this->count++);

    PageContext::instance()->set('textboxvalue', 'Szöveg');

    return 'Helloka '.$name . ' '.$prefix . ' '.$boo;
  }

  public function bugger($bugid){

    $em = Zool::app()->em;
    $bug = $em->find('app\model\Bug', $bugid);

      SessionContext::instance()->set('bugstringm', 'Selected: '.$bug->description);
      SessionContext::instance()->set('selectedBug', $bug);
  }

  public function callThis(){
    $this->string++;
    if($this->string > 40 )$this->string = 35;

    $this->llist = array();

    SessionContext::instance()->set('string', $this->string);
    return 'called me';
  }

  public function dummy(){}

  public function getList(){
    return $this->list;
  }

}