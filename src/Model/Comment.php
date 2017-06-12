<?php

namespace Akademiano\Content\Comments\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\Entity;
use Akademiano\Entity\NamedEntityInterface;

class Comment extends ContentEntity implements NamedEntityInterface
{
    /** @var  Entity */
    protected $entity;

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
