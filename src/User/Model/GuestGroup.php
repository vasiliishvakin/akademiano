<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;


use DeltaDb\AbstractEntity;

class GuestGroup extends AbstractEntity
{
    protected $id = 0;
    protected $name = "guest";

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

} 