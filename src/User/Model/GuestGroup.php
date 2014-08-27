<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;


use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;

class GuestGroup extends AbstractEntity implements EntityInterface
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