<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Relations;


use DeltaDb\EntityInterface;
use DeltaDb\Repository;

class mnRelationsManager extends Repository
{
    const FIRST_ENTITY_FIELD = "firstItem";
    const SECOND_ENTITY_FIELD = "secondItem";

    protected $name;

    /** @var  Repository */
    protected $firstManager;

    /** @var  Repository */
    protected $secondManager;

    protected $firstFieldName;
    protected $secondFieldName;

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

    /**
     * @return mixed
     */
    public function getFirstFieldName()
    {
        if (is_null($this->firstFieldName)) {
            $this->firstFieldName = $this->getFirstManager()->getTableName() . "_item";
        }
        return $this->firstFieldName;
    }

    /**
     * @param mixed $firstFieldName
     */
    public function setFirstFieldName($firstFieldName)
    {
        $this->firstFieldName = $firstFieldName;
    }

    /**
     * @return mixed
     */
    public function getSecondFieldName()
    {
        if (is_null($this->secondFieldName)) {
            $this->secondFieldName = $this->getSecondManager()->getTableName() . "_item";
        }
        return $this->secondFieldName;
    }

    /**
     * @param mixed $secondFieldName
     */
    public function setSecondFieldName($secondFieldName)
    {
        $this->secondFieldName = $secondFieldName;
    }

    public function getMetaInfo()
    {
        if (is_null($this->metaInfo)) {
            $firstTable = $this->getFirstManager()->getTableName();
            $secondTable = $this->getSecondManager()->getTableName();
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
     * @return EntityInterface|void
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
        foreach ($data["fields"] as $field=>$value) {
            if ($value instanceof EntityInterface) {
                $data["fields"][$field] = $value->getId();
            }
        }
        return $data;
    }

} 