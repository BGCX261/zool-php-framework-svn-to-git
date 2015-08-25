<?php

namespace app\model;

/**
 * @Entity @Table(name="bugs")
 **/
class Bug
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;
    /**
     * @Column(type="string")
     **/
    public $description;
    /**
     * @Column(type="datetime")
     **/
    public $created;
    /**
     * @Column(type="string")
     **/
    public $status;

    public function setStatus($status){
      $this->status = $status;
    }

}