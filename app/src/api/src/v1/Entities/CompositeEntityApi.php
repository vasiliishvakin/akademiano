<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\RelationEntity;
use Akademiano\Entity\Uuid;
use Akademiano\Entity\UuidInterface;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\ClassTools;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\StringUtils;
use function foo\func;

class CompositeEntityApi extends EntityApi
{
    const RELATIONS = [];

    public function getRelatedAttributes(): array
    {
        return (array) ClassTools::getClassTreeArrayConstant(get_class($this), 'RELATIONS');
    }

    public function getRelatedAttributeApi(string $apiId): RelationEntityApi
    {
        $apiGetMethod = 'get' . ucfirst($apiId);
        if (!method_exists($this, $apiGetMethod)) {
            throw new \LogicException(sprintf('Not exist method "%s" to get relation api object by id "%s" in class "%s"', $apiGetMethod, $apiId, get_class($this)));
        }
        /** @var RelationEntityApi $api */
        $api = $this->$apiGetMethod();
        if (!$api instanceof RelationEntityApi) {
            throw new \LogicException(sprintf('Method "%s" must return instance of "%s" but return "%s"', $apiGetMethod, RelationEntityApi::class, get_class($api)));
        }
        return $api;
    }

    public function updateRelations(EntityInterface $entity, string $relatedAttributeApiID, string $relatedAttributeName)
    {
        $method = 'get' . ucfirst($relatedAttributeName);
        /** @var Collection|EntityInterface[] $currentItems */
        $currentItems = $entity->$method();

        $currentItemsIds = $currentItems->lists('id')->toArray();
        $currentItemsIds = array_map(function (UuidInterface $value) {
            return $value->getInt();
        }, $currentItemsIds);

        $relatedAttributeApi = $this->getRelatedAttributeApi($relatedAttributeApiID);

        $currentRelationEntityField = $relatedAttributeApi->getFieldName($entity);
        $anotherRelationEntityField = $relatedAttributeApi->getAnotherField($currentRelationEntityField);
        $anotherRelationMethod = 'get' . ucfirst($anotherRelationEntityField);

        /** @var Collection|RelationEntity[] $currentRelations */
        $currentRelations = $relatedAttributeApi->find([$currentRelationEntityField => $entity]);
        //clear old relations and get new
        $alreadyRelatedItemsIds = [];
        foreach ($currentRelations as $relation) {
            $relationItemIdInt = $relation->$anotherRelationMethod()->getId()->getInt();
            if (!in_array($relationItemIdInt, $currentItemsIds)) {
                $relatedAttributeApi->deleteEntity($relation);
            } else {
                $alreadyRelatedItemsIds[] = $relationItemIdInt;
            }
        }
        $unrelatedItemsIds = array_diff($currentItemsIds, $alreadyRelatedItemsIds);

        $unrelatedItems = $currentItems->filter(
            function (EntityInterface $item) {
                return $item->getId()->getInt();
            },
            $unrelatedItemsIds,
            'in'
        );

        foreach ($unrelatedItems as $item) {
            $relatedAttributeApi->save([
                $currentRelationEntityField => $entity,
                $anotherRelationEntityField => $item,
            ]);
        }
    }

    public function save(array $data)
    {
        $relatedAttributes = $this->getRelatedAttributes();
        foreach ($relatedAttributes as $attribute => $apiId) {
            if (!array_key_exists($attribute, $data)) {
                $attribute = StringUtils::camelCaseToLowDash($attribute);
                if (!array_key_exists($attribute, $data)) {
                    continue;
                }
            }
            $data[$attribute] = array_map(function ($value) {
                return Uuid::normalize($value);
            }, $data[$attribute]);
            $data[$attribute] = ArrayTools::filterNulls($data[$attribute]);
        }
        return parent::save($data);
    }

    public function saveEntity(EntityInterface $entity)
    {
        $entity = parent::saveEntity($entity);

        $relatedAttributes = $this->getRelatedAttributes();

        foreach ($relatedAttributes as $attribute => $apiId) {
            $this->updateRelations($entity, $apiId, $attribute);
        }
        return $entity;
    }

    public function clearRelations(EntityInterface $entity, string $relatedAttributeApiID, string $relatedAttributeName)
    {
        $relatedAttributeApi = $this->getRelatedAttributeApi($relatedAttributeApiID);

        $currentRelationEntityField = $relatedAttributeApi->getFieldName($entity);
        /** @var Collection|RelationEntity[] $currentRelations */
        $currentRelations = $relatedAttributeApi->find([$currentRelationEntityField => $entity]);
        foreach ($currentRelations as $relation) {
            $relatedAttributeApi->deleteEntity($relation);
        }
    }

    public function deleteEntity(EntityInterface $entity)
    {
        $relatedAttributes = $this->getRelatedAttributes();

        foreach ($relatedAttributes as $attribute => $apiId) {
            $this->clearRelations($entity, $apiId, $attribute);
        }
        return parent::deleteEntity($entity);
    }
}
