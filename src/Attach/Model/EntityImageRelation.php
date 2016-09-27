<?php


namespace Attach\Model;


class EntityImageRelation extends EntityFileRelation
{
    public function __construct()
    {
        $this->setFirstClass(Entity::class);
        $this->setSecondClass(ImageFileEntity::class);
    }
}