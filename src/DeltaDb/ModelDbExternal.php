<?php

class ModelDbExternal extends ModelDbBase
{
    protected $class;
    protected $table;
    protected $dbFields;

    public function __construct($class, $table)
    {
        $this->setClass($class);
        $this->setTable($table);
    }

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

    /**
     * @param mixed $table
     */
    protected function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    public function getDao()
    {
        throw new LogicException('not implemented');
    }

    protected function createEntity($data)
    {
        $class = $this->getClass();
        /** @var $entity ModelDb */
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
     * @return ModelDb[]
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
            /** @var ModelDb $obj */
            $class = $this->getClass();
            $obj = new $class();
            $this->dbFields = $obj->getDbFieldsList();
        }
        return $this->dbFields;
    }
}