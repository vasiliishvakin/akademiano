<?php

namespace Akademiano\EntityOperator\Worker;

use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\RelationsBetweenTrait;
use Akademiano\EntityOperator\Command\FindRelatedCommand;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\RelationEntity;

class RelationsWorker extends EntitiesWorker
{
    const FIELD_FIRST = "first";
    const FIELD_SECOND = "second";

    const FIRST_CLASS = RelationEntity::FIRST_CLASS;
    const SECOND_CLASS = RelationEntity::SECOND_CLASS;


    const WORKER_ID = 'relationsWorker';
    const TABLE_NAME = 'relations';
    const FIELDS = [self::FIELD_FIRST, self::FIELD_SECOND];

    const EXT_ENTITY_FIELDS = ['first', 'second'];

    use RelationsBetweenTrait;

    public static function getSupportedCommands(): array
    {
        $commands = parent::getSupportedCommands();
        $commands[] = FindRelatedCommand::class;
        return $commands;
    }

    public static function getEntityClassForMapFilter()
    {
        return RelationEntity::class;
    }

    public function getFirstField()
    {
        return static::FIELD_FIRST;
    }

    public function getSecondField()
    {
        return static::FIELD_SECOND;
    }

    /**
     * @return mixed
     */
    public function getFirstClass()
    {
        return static::FIRST_CLASS;
    }

    /**
     * @return mixed
     */
    public function getSecondClass()
    {
        return static::SECOND_CLASS;
    }

    public function execute(CommandInterface $command)
    {
        switch (true) {
            default:
                return parent::execute($command);
        }
    }

    public function findRelations(EntityInterface $entity)
    {
        $knownField = $this->getFieldName($entity);

        $criteria = [$knownField => $entity->getId()->getInt()];
        /** @var EntityOperator $operator */
        $operator = $this->getOperator();
        $relations = $operator->find(static::ENTITY_CLASS, $criteria);
        return $relations;
    }

    public function findRelated(EntityInterface $entity)
    {
        $relations = $this->findRelations($entity);

        $wantedClass = $this->getAnotherClass($entity);
        $unknownField = $this->getAnotherField($entity);
        $ids = $relations->lists($unknownField);

        /** @var EntityOperator $operator */
        $operator = $this->getOperator();
        $entities = $operator->find($wantedClass, ["id" => $ids]);
        return $entities;
    }
}
