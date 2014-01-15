<?php

namespace DeltaDb;

/**
 * Class Repository
 * @package DeltaDb
 * @method  __construct(array $params) Params: ['class', 'table', 'dbaName' => null]
 */
class Repository extends Table
{
    protected $class;
    protected $dbFields;

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    protected function createEntity($data)
    {
        $class = $this->getClass();
        /** @var $entity AbstractObject */
        $entity = new $class;
        $entity->load($data);
        $entity->setLoaded();
        return $entity;
    }

    public function getById($id)
    {
        $data = $this->getByIdRaw($id);
        return (empty($data)) ? null : $this->createEntity($data);
    }

    public function getAll()
    {
        $data = $this->getAllRaw();
        return (empty($data)) ? [] : $this->createEntities($data);
    }

    /**
     * @param array $data
     * @param bool $keyFromId
     * @return AbstractObject[]
     */
    public function createEntities(array $data, $keyFromId = false)
    {
        $entities = [];
        foreach ($data as $row) {
            $entity = $this->createEntity($row);
            if ($keyFromId) {
                $entities[$entity->getId()] = $entity;
            } else {
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    public function deleteById($id)
    {
        return $this->deleteRaw($id);
    }

    public function getDbFields()
    {
        if (is_null($this->dbFields)) {
            /** @var AbstractObject $obj */
            $class = $this->getClass();
            $obj = new $class();
            $this->dbFields = $obj->getDbFieldsList();
        }
        return $this->dbFields;
    }

    public function filterToDbFields(array $data)
    {
        $dbFields = $this->getRepository()->getDbFields();
        $dbFields[] = 'id';
        $dbData = array_intersect_key($data, rray_flip($dbFields));
        $dbData = array_filter($dbData, function ($var) {return !is_null($var);});
        return $dbData;
    }
}