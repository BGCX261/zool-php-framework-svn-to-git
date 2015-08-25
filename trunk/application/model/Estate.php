<?php

namespace app\model;

/**
 * @Entity @Table(name="estates")
 **/
class Estate
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    public $id;

    /**
     * @Column(type="string")
     **/
    public $name;

    /**
     * @Column(type="datetime")
     **/
    public $created;

    /**
     * @Column(type="string")
     **/
    public $status;

     /**
     * @ManyToOne(targetEntity="User", cascade={"persist"})
     * @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    public $owner;

    public function toString(){
      return "Estate[id=$this->id, name=$this->name, created={$this->created->format('Y-m-d')}, status=$this->status, owner={$this->owner->toString()}]";
    }


}