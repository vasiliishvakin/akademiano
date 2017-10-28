<?php

namespace Akademiano\EntityOperator\Worker;

use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Worker\RelationsBetweenTrait;
use Akademiano\EntityOperator\Command\FindRelatedCommand;
use Akademiano\EntityOperator\Entity\RelationEntity;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;

class RelationsWorker extends PostgresWorker implements DelegatingInterface
{
    const FIELD_FIRST = "first";
    const FIELD_SECOND = "second";

    const FIRST_CLASS = RelationEntity::FIRST_CLASS;
    const SECOND_CLASS = RelationEntity::SECOND_CLASS;

    const TABLE_ID_INC = 1;
    const TABLE_ID = 4;
    const TABLE_NAME = "relations";
    const EXPAND_FIELDS = ["first", "second"];
    const ENTITY_CLASS = RelationEntity::class;

    use DelegatingTrait;
    use RelationsBetweenTrait;

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
        return static::ENTITY_CLASS;
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
        switch ($command->getName()) {
            case FindRelatedCommand::COMMAND_NAME : {
                $entity = $command->getParams("entity");
                return $this->findRelated($entity);
                break;
            }
            default: {
                return parent::execute($command);
            }
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
