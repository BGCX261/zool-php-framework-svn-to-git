<?php

namespace app\model;

/**
 * @Entity @Table(name="users")
 **/
class User
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    protected $id;

    /**
     * @Column(type="string")
     **/
    protected $username;

    /**
     * @Column(type="string")
     **/
    protected $description;
    /**
     * @Column(type="datetime")
     **/
    protected $created;
    /**
     * @Column(type="string")
     **/
    protected $status;

}