<?php

namespace app\controller;

use app\model\User;

use zool\Zool;

use app\model\Estate;

use zool\controller\ZController;

/**
 *
 * @scope(EVENT_SCOPE)
 *
 */
class StartupController extends ZController{


  public function generate(){

    $em = $this->em = Zool::app()->entityManager;

    $statuses = array('new', 'seld', 'old');
    
    $cities = array('Szeged', 'Budapest', 'Torinó', 'Tokyo', 'Debrecen', 'Kiskőrös', 'Kecskemét', 'Mucsaröcsöge', 'Aszód',
    'Makó', 'Lajosmizse', 'Budafok', 'Kerepes', 'Tarcsa', 'Mórahalom', 'Sopron', 'Zalaegerszeg', 'Jászkarajenő');
    
    $types = array('lakás', 'tanya', 'téglaépítésű', 'pince', 'garázs', 'borospince', 'garzon');

   $connection = $this->em->getConnection();
    $platform   = $connection->getDatabasePlatform();

    try{
       $connection->executeUpdate('DROP TABLE estates; DROP TABLE users;');
    }catch(\Exception $e){}

      $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
     $classes = array(
       $em->getClassMetadata('app\model\User'),
       $em->getClassMetadata('app\model\Estate'),
     );
     $tool->updateSchema($classes);


    $user = new User();
    $user->username = 'user';
    $user->password = md5('pass');

    $this->em->persist($user);
    $this->em->flush();

    for($i=1; $i<100; $i++){
      $estate = new Estate();
      $estate->name = $cities[array_rand($cities)].'i '. $types[array_rand($types)];
      
      $date = new \DateTime();
      $date->setDate(rand(1900, 2012), rand(1,12), rand(1,28));
      
      $estate->created = $date;
      $estate->status = $statuses[array_rand($statuses)];
      $estate->owner = $user;

      $this->em->persist($estate);
      $this->em->flush();
    }

    $user = new User();
    $user->username = 'admin';
    $user->password = md5('pass');

    $this->em->persist($user);
    $this->em->flush();

    for($i=1; $i<100; $i++){
      $estate = new Estate();
      $estate->name = 'Admin Estate_'.$i;
      $estate->created = new \DateTime();
      $estate->status = $statuses[array_rand($statuses)];
      $estate->owner = $user;

      $this->em->persist($estate);
      $this->em->flush();
    }

  }

  public function client() {
    return Zool::app()->viewProvider->getClientType();
  }

}