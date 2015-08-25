<?php

namespace app\model;

/**
 * @Entity @Table(name="users")
 **/
class User{

    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;

    /**
     *
     * @Column(type="string", unique=true)
     */
    public $username;

    /**
     * @Column(type="string")
     */
    public $password;

    public function toString(){
      return "User[id=$this->id, username=$this->username]";
    }

}