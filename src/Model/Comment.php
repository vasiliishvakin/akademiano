<?php

namespace Akademiano\Content\Comments\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\Entity;
use Akademiano\Entity\NamedEntityInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\UserEO\Model\Utils\OwneredTrait;

class Comment extends ContentEntity implements NamedEntityInterface, DelegatingInterface
{
    use DelegatingTrait;
    use OwneredTrait;

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
