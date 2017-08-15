<?php

namespace Akademiano\Attach\Model;

use Akademiano\Content\Files\Model\File;
use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\HttpWarp\Exception\NotFoundException;

/**
 * Class LinkedFile
 * @package Akademiano\Attach\Model
 * @method EntityOperator getOperator()
 */
class LinkedFile extends File
{
    const ENTITY_CLASS = Entity::class;

    /** @var  EntityInterface */
    protected $entity;

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        if (null !== $this->entity && !$this->entity instanceof EntityInterface) {
            $entity = $this->getOperator()->get(static::ENTITY_CLASS, $this->entity);
            if (!$entity) {
                throw new NotFoundException(sprintf('Entity of class "%s" with id "%s" not found'), static::ENTITY_CLASS, $this->entity);
            }
            $this->entity = $entity;
        }
        return $this->entity;
    }

    /**
     * @param EntityInterface $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
