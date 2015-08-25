<?php

namespace app\controller;

/**
 *
 * @scope(EVENT_SCOPE)
 */
use zool\Zool;

use zool\context\RequestContext;

use zool\context\SessionContext;

use zool\context\Contexts;

use app\model\User;

use zool\controller\ZController;

class IdentityController extends ZController{


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

  public function login(){

    $user = $this->em->getRepository('app\model\User')->findOneBy(array(
      'username'=> $this->contexts->get('loginUsername'),
      'password'=> md5($this->contexts->get('loginPassword'))
    ));

    if(null == $user){
      $this->reqCon->set('loginMessage', 'Login failed (user/pass or admin/pass)');
    }else{
      $this->sesCon->set('identity', $user);
    }

  }

  public function logout(){
    $this->sesCon->deset('identity');
  }

}