<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Entity\EntityInterface;

abstract class EntityObjectCommand extends EntityCommand implements EntityObjectCommandInterface
{
    /** @var EntityInterface */
    protected $entity;

    /**
     * EntityObjectCommand constructor.
     * @param EntityInterface $entity
     */
    public function __construct(EntityInterface $entity)
    {
        parent::__construct(get_class($entity));
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }
}
