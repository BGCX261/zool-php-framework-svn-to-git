<?php

namespace app\controller;

use zool\context\RequestContext;

use zool\context\Contexts;

use app\model\Bug;

use zool\context\SessionContext;

use zool\Zool;

use zool\controller\ZController;

/**
 * @scope(EVENT_SCOPE)
 */
class BugController extends ZController{


  private $contexts;
  private $reqCon;
  private $em;
  private $sesCon;

  public function beforeAction(){
    $this->contexts = Contexts::instance();
    $this->reqCon = RequestContext::instance();
    $this->sesCon = SessionContext::instance();
    $this->em = Zool::app()->em;
  }

  public function save($id){
    //$id = Contexts::instance()->get('bug_id');
    $em = $this->em;

    Zool::app()->viewProvider->envelope->message =
    array('title'=>'Mentés','content'=>'Mentés sikeres');

    $this->reqCon->set('buge1', $this->reqCon->get('buge'));
    $this->reqCon->deset('buge');
  }

  public function bugger($bugid){

    $em = Zool::app()->em;
    $bug = $em->find('app\model\Bug', $bugid);

    SessionContext::instance()->set('bugstringm', 'Selected: '.$bug->description);
    SessionContext::instance()->set('selectedBug', $bug);

    SessionContext::instance()->set('bug_id', $bug->id);
    SessionContext::instance()->set('bug_description', $bug->description);
    SessionContext::instance()->set('bug_created', $bug->created);
  }

}