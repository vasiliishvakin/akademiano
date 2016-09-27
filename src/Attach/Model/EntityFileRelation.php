<?php


namespace Attach\Model;


use DeltaPhp\Operator\Entity\Entity;
use DeltaPhp\Operator\Entity\RelationEntity;

class EntityFileRelation extends RelationEntity
{
    public function __construct()
    {
        $this->setFirstClass(Entity::class);
        $this->setSecondClass(FileEntity::class);
    }
}