<?php

namespace Akademiano\EntityOperator\Worker;

use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\RelationsBetweenTrait;
use Akademiano\EntityOperator\Command\FindRelatedCommand;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\RelationEntity;

class RelationsWorker extends EntitiesWorker
{
    public const WORKER_ID = 'relationsWorker';

    public const FIRST_FIELD = "first";
    public const SECOND_FIELD = "second";

/*    protected const FIRST_CLASS = RelationEntity::FIRST_CLASS;
    protected const SECOND_CLASS = RelationEntity::SECOND_CLASS;*/

    public const TABLE_NAME = 'relations';

    protected const FIELDS = [self::FIRST_FIELD, self::SECOND_FIELD];

    protected const EXT_ENTITY_FIELDS = ['first', 'second'];

    public const ENTITY = RelationEntity::class;

    use RelationsBetweenTrait;

    public static function getSupportedCommands(): array
    {
        $commands = parent::getSupportedCommands();
        $commands[] = FindRelatedCommand::class;
        return $commands;
    }

    public function getFirstField()
    {
        return constant(static::FIRST_FIELD);
    }

    public function getSecondField()
    {
        return constant(static::SECOND_FIELD);
    }

    /**
     * @return mixed
     */
    public function getFirstClass()
    {
        return constant(static::ENTITY . '::FIRST_CLASS');
    }

    /**
     * @return mixed
     */
    public function getSecondClass()
    {
        return constant(static::ENTITY . '::SECOND_CLASS');
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
