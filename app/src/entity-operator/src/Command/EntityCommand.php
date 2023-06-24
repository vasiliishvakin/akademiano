<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Entity\Entity;

abstract class EntityCommand implements EntityCommandInterface
{
    /** @var string */
    protected $entityClass;

    public function __construct(string $class = Entity::class)
    {
        $this->setEntityClass($class);
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    protected function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }
}
