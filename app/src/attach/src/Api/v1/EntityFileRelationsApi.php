<?php


namespace Akademiano\Attach\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Attach\Model\EntityFileRelation;
use Akademiano\Attach\Model\LinkedFile;
use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Entity\RelationEntity;
use Akademiano\EntityOperator\Worker\RelationsBetweenTrait;
use Akademiano\EntityOperator\Worker\RelationsWorker;
use Akademiano\Utils\Object\Collection;

class EntityFileRelationsApi extends EntityApi
{
    const API_ID = "entityFileRelationsApi";
    const ENTITY_CLASS = EntityFileRelation::class;

    const FIRST_CLASS = Entity::class;
    const SECOND_CLASS = Entity::class;

    const FIELD_FIRST = RelationsWorker::FIELD_FIRST;
    const FIELD_SECOND = RelationsWorker::FIELD_SECOND;

    use RelationsBetweenTrait;

    public function getFirstClass()
    {
        return static::FIRST_CLASS;
    }

    public function getSecondClass()
    {
        return static::SECOND_CLASS;
    }

    public function getFirstField()
    {
        return static::FIELD_FIRST;
    }

    public function getSecondField()
    {
        return static::FIELD_SECOND;
    }

    public function saveRelation(Entity $task, LinkedFile $file)
    {
        return $this->save([
            $this->getFirstField() => $task,
            $this->getSecondField() => $file,
        ]);
    }

    /**
     * @param EntityInterface $entity
     * @return RelationEntity[]|Collection
     */
    public function findRelations(EntityInterface $entity)
    {
        $field = $this->getFieldName($entity);
        $criteria = [$field => $entity];
        $relations = $this->findAll($criteria);
        return $relations;
    }

    public function deleteByRelated(EntityInterface $entity)
    {
        $relations = $this->findRelations($entity);
        foreach ($relations as $relation) {
            $this->deleteEntity($relation);
        }
    }
}
