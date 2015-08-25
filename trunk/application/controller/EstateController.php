<?php

namespace app\controller;

use zool\context\SessionContext;

use zool\context\RequestContext;

use app\model\Estate;

use zool\Zool;

use zool\context\Contexts;

use zool\controller\ZController;
/*
 * @scope(EVENT_SCOPE)
 */
class EstateController extends ZController{

  public function getUserEstates(){

    $user = Contexts::instance()->get('identity');
    $em = Zool::app()->entityManager;

    $estates = $em->createQuery('select b from app\model\Estate b where b.owner = :owner')
    ->setParameter('owner',$user->id)
    ->setFirstResult(1)->setMaxResults(rand(20,30))
    ->getResult();

    RequestContext::instance()->set('estatesList', $estates);
  }

  public function search(){
    $user = Contexts::instance()->get('identity');
    $em = Zool::app()->entityManager;

    $search = Contexts::instance()->get('estate_search');

    $estates = $em->createQuery('select b from app\model\Estate b where b.owner = :owner and b.name = :name')
    ->setParameter('owner',$user->id)
    ->setParameter('name', "%$search%")
    ->getResult();

    RequestContext::instance()->set('estatesList', $estates);
  }

  public function info(Estate $estate){

    ob_start();
    var_dump($estate);
    return ob_get_clean();

  }

  public function save($id){
    $em = Zool::app()->entityManager;
    $estate = $em->find('app\model\Estate', $id);

    list($y, $m, $d) = explode('-',  Contexts::instance()->get('estate_created'));

    SessionContext::instance()->set('created_msg', Contexts::instance()->get('estate_created'));

    $estate->name = Contexts::instance()->get('estate_name');
    $date = new \DateTime();
    $date->setDate($y, $m, $d);
    $estate->created = $date;
    $em->persist($estate);
    $em->flush();
  }



  public function getUsers(){
    return Zool::app()->entityManager->createQuery('select b from app\model\User b')->getResult();
  }

  public function select($id){
    $em = Zool::app()->entityManager;
     $estate = $em->find('app\model\Estate', $id);
     SessionContext::instance()->set('estate', $estate);

     Zool::app()->viewProvider->envelope->message =
    array('title'=>'Mentés','content'=>$estate->name);
  }

}