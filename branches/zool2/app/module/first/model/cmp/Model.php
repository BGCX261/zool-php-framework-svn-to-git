<?php

namespace first\model\cmp;

/**
 * @Entity @Table(name="nested_model")
 **/
class Model
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     **/
    protected $id;

    /**
     * @Column(type="string")
     **/
    protected $description;

    /**
     * @Column(type="string")
     **/
    protected $status;

}