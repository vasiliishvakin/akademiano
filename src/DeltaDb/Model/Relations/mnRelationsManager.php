<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Relations;


use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use DeltaUtils\Parts\InnerCache;
use DeltaUtils\StringUtils;

class mnRelationsManager extends Repository
{
    const FIRST_ENTITY_FIELD = "firstItem";
    const SECOND_ENTITY_FIELD = "secondItem";

    const FIRST_PART = "firstPart";
    const SECOND_PART = "secondPart";

    protected $name;

    /** @var  Repository */
    protected $firstManager;

    /** @var  Repository */
    protected $secondManager;

    protected $firstFieldName;
    protected $secondFieldName;

    protected $firstName;
    protected $secondName;

    protected $metaInfo;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Repository
     */
    public function getFirstManager()
    {
        return $this->firstManager;
    }

    /**
     * @param Repository $firstManager
     */
    public function setFirstManager($firstManager)
    {
        $this->firstManager = $firstManager;
    }

    /**
     * @return Repository
     */
    public function getSecondManager()
    {
        return $this->secondManager;
    }

    /**
     * @param Repository $secondManager
     */
    public function setSecondManager($secondManager)
    {
        $this->secondManager = $secondManager;
    }

    public function getPartManager($part)
    {
        switch ($part) {
            case self::FIRST_PART :
                return $this->getFirstManager();
                break;
            case self::SECOND_PART :
                return $this->getSecondManager();
        }
    }

    public function getOtherPartManager($part)
    {
        $part = $this->getOtherPartName($part);
        return $this->getPartManager($part);
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        if (is_null($this->firstName)) {
            $this->firstName = $this->getFirstManager()->getTableName();
        }
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getSecondName()
    {
        if (is_null($this->secondName)) {
            $this->secondName = $this->getSecondManager()->getTableName();
        }
        return $this->secondName;
    }


    /**
     * @return mixed
     */
    public function getFirstFieldName()
    {
        if (is_null($this->firstFieldName)) {
            $this->firstFieldName = $this->getFirstName() . "_item";
        }
        return $this->firstFieldName;
    }

    /**
     * @return mixed
     */
    public function getSecondFieldName()
    {
        if (is_null($this->secondFieldName)) {
            $this->secondFieldName = $this->getSecondName() . "_item";
        }
        return $this->secondFieldName;
    }

    public function getMetaInfo()
    {
        if (is_null($this->metaInfo)) {
            $firstTable = $this->getFirstName();
            $secondTable = $this->getSecondName();
            $table = "{$firstTable}_{$secondTable}_relations";
            $this->metaInfo = [
                $table => [
                    "class"  => "\\DeltaDb\\Model\\Relations\\mnRelation",
                    "id"     => "id",
                    "fields" => [
                        "id",
                        $this->getFirstFieldName(),
                        $this->getSecondFieldName()
                    ]
                ]
            ];
        }
        return parent::getMetaInfo();
    }

    public function create(array $data = null, $entityClass = null)
    {
        /** @var mnRelation $item */
        $item = parent::create($data, $entityClass);
        $item->setFirstManager($this->getFirstManager());
        $item->setSecondManager($this->getSecondManager());
        return $item;
    }

    public function findRaw(array $criteria = [], $table = null, $limit = null, $offset = null, $orderBy = null)
    {
        $adapter = $this->getAdapter();
        if (is_null($table)) {
            $table = $this->getTableName();
        }
        $where = $adapter->getWhere($criteria);

        $fields = $this->getFieldsList($table);
        $fields = implode(", ", $fields);

        $query = "SELECT  {$fields}
            from {$table}
            {$where}";
        $orderStr = $adapter->getOrderBy($orderBy);
        $query .= $orderStr;
        $limitSql = $adapter->getLimit($limit, $offset);
        $query .= $limitSql;
        $whereParams = $adapter->getWhereParams($criteria);
        array_unshift($whereParams, $query);
        $result = call_user_func_array([$adapter, 'select'], $whereParams);
        return $result;
    }

    /**
     * @param mnRelation $entity
     * @param array $data
     * @return mnRelation|void
     */
    public function load(EntityInterface $entity, array $data)
    {
        parent::load($entity, $data);
        $firstField = $this->getFirstFieldName();
        $secondField = $this->getSecondFieldName();
        if (isset($data[$firstField])) {
            $this->setField($entity, self::FIRST_ENTITY_FIELD, $data[$firstField]);
        } elseif (isset($data[self::FIRST_ENTITY_FIELD])) {
            $this->setField($entity, self::FIRST_ENTITY_FIELD, $data[self::FIRST_ENTITY_FIELD]);
        }
        if (isset($data[$secondField])) {
            $this->setField($entity, self::SECOND_ENTITY_FIELD, $data[$secondField]);
        } elseif (isset($data[self::SECOND_ENTITY_FIELD])) {
            $this->setField($entity, self::SECOND_ENTITY_FIELD, $data[self::SECOND_ENTITY_FIELD]);
        }
        return $entity;
    }

    public function reserve(EntityInterface $entity)
    {
        $data = parent::reserve($entity);
        $firstField = $this->getFirstFieldName();
        $secondField = $this->getSecondFieldName();
        $data["fields"][$firstField] = $this->getField($entity, self::FIRST_ENTITY_FIELD);
        $data["fields"][$secondField] = $this->getField($entity, self::SECOND_ENTITY_FIELD);
        foreach ($data["fields"] as $field => $value) {
            if ($value instanceof EntityInterface) {
                $data["fields"][$field] = $value->getId();
            }
        }
        return $data;
    }

    public function getFieldPartName($part)
    {
        switch ($part) {
            case self::FIRST_PART :
                $field = $this->getFirstFieldName();
                break;
            case self::SECOND_PART :
                $field = $this->getSecondFieldName();
                break;
        }
        return $field;
    }

    public function getOtherFieldPartName($part)
    {
        switch ($part) {
            case self::FIRST_PART :
                $field = $this->getSecondFieldName();
                break;
            case self::SECOND_PART :
                $field = $this->getFirstFieldName();
                break;
        }
        return $field;
    }

    public function getOtherPartName($part)
    {
        switch ($part) {
            case self::FIRST_PART :
                return self::SECOND_PART;
                break;
            case self::SECOND_PART :
                return self::FIRST_PART;
                break;
        }
    }

    public function filterPartName($part)
    {
        if (in_array($part, [self::FIRST_PART, self::SECOND_PART])) {
            return $part;
        }

        if ($part === $this->getFirstName()) {
            return self::FIRST_PART;
        }
        if ($part === $this->getSecondName()) {
            return self::SECOND_PART;
        }
    }

    /**
     * @param integer|string|EntityInterface $object
     * @return integer
     */
    public function filterPartObject2Id($object)
    {
        return (integer) ($object instanceof EntityInterface) ? $object->getId() : $object;
    }


    /**
     * @param array $partItems
     * @param string $part
     * @deprecated
     */
    public function findPartsIds(array $partItems, $part = self::FIRST_PART)
    {
        throw new \BadMethodCallException();
    }

    public function findOtherPartIds(array $partItems, $part = self::FIRST_PART)
    {
        $part = $this->filterPartName($part);
        $field = $this->getFieldPartName($part);
        $criteria = [$field => $partItems];
        $data = $this->findRaw($criteria);
        $ids = [];
        $otherField = $this->getOtherFieldPartName($part);
        foreach ($data as $row) {
            $ids[] = $row[$otherField];
        }
        return $ids;
    }

    public function deleteByPartId($partId, $part = self::FIRST_PART)
    {
        $partId = $this->filterPartObject2Id($partId);
        $part = $this->filterPartName($part);
        $fieldName = $this->getFieldPartName($part);
        return $this->deleteBy([$fieldName => $partId]);
    }

    /**
     * @param $partId
     * @param $relationIds
     * @param string $part
     */
    public function saveForPartId($partId, $relationIds, $part = self::FIRST_PART)
    {
        $part = $this->filterPartName($part);
        $partId = $this->filterPartObject2Id($partId);
        $firstFieldName = $this->getFieldPartName($part);
        $secondFieldName = $this->getOtherFieldPartName($part);
        if (!is_array($relationIds)) {
            $relationIds = [$relationIds];
        }
        foreach ($relationIds as $relationId) {
            $relationId = $this->filterPartObject2Id($relationId);
            $relation = $this->create([$firstFieldName => $partId, $secondFieldName => $relationId]);
            $this->save($relation);
        }
    }

    public function updateForPartId($partId, $relationIds, $part = self::FIRST_PART)
    {
        $part = $this->filterPartName($part);
        $this->deleteByPartId($partId, $part);
        $this->saveForPartId($partId, $relationIds, $part);
    }

    public function getFieldNumName($fieldName)
    {
        return ($fieldName === $this->getFirstFieldName()) ? "First" :
            ($fieldName === $this->getSecondFieldName()) ? "Second" : null;
    }

    public function getOtherFieldNumName($fieldName)
    {
        return ($fieldName === $this->getFirstFieldName()) ? "Second" :
            ($fieldName === $this->getSecondFieldName()) ? "First" : null;
    }

    public function getJoinPart($conditionName)
    {
        $mainNum = $this->getOtherFieldNumName($conditionName);
        $method = "get{$mainNum}FieldName";
        $mainField = $this->$method();
        $method = "get{$mainNum}Manager";
        /** @var Repository $mainManager */
        $mainManager = $this->$method();
        $mainTable = $mainManager->getTableName();
        $currentTable = $this->getTableName();
        return " join {$currentTable}  on {$currentTable}.{$mainField}={$mainTable}.id";
    }

    public function getWhereFieldFullName($conditionName)
    {
        $fieldNum = $this->getFieldNumName($conditionName);
        $method = "get{$fieldNum}FieldName";
        $fieldName = $this->$method();
        return $this->getTableName() . "." . $fieldName;
    }

    public function findOthers($currentId, $currentName)
    {
        $currentName = StringUtils::camelCaseToLowDash($currentName);
        $currentPart = $this->filterPartName($currentName);
        $otherPart = $this->getOtherPartName($currentPart);
        $othersIds = $this->findOtherPartIds([$currentId], $currentPart);
        $otherManager = $this->getPartManager($otherPart);
        $items = $otherManager->findByIds($othersIds);
        return $items;
    }
}