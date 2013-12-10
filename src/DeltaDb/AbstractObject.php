<?php

namespace DeltaDb;

abstract class AbstractObject extends Table
{
    // change in class
    protected static $classMapTableName;
    protected static $repositoryClass = '\DeltaDb\Repository\Repository';

    //do not touch
    protected static $repository;

    protected $isNewEntity = true;
    protected $id;
    protected $dbFieldsList;

    public static function repository()
    {
        if (is_null(static::$repository)) {
            $repository = static::$repositoryClass;
            static::$repository = new $repository(get_called_class(), static::$table);
        }
        return static::$repository;
    }

    public function getExcludedFields()
    {
        return ['table', 'modelClass', 'modelTplClass', 'isNewEntity', 'modelExternal', 'modelTpl', 'id', 'dbFieldsList'];
    }

    /**
     * Do not Use!!!
     * @param mixed $table
     * @throws \LogicException
     */
    public function setTableName($table)
    {
        throw new \LogicException('You cannot change table in class object');
    }


    public function getTableName()
    {
        return static::$classMapTableName;
    }

    public function isNew()
    {
        return (bool) $this->isNewEntity;
    }

    /**
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = (integer) $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if ($this->isNew()) {
            $this->save();
        }
        return $this->id;
    }

    public function getDbFieldsList()
    {
        if (is_null($this->dbFieldsList)) {
            $vars = get_object_vars($this);
            $dbVars = array_diff_key($vars, array_flip($this->getExcludedFields()));
            $this->dbFieldsList = array_keys($dbVars);
        }
        return $this->dbFieldsList;
    }

    public function getDbFields()
    {
        $fieldsList = $this->getDbFieldsList();
        $dbFields = [];
        foreach ($fieldsList as $field) {
            $method = 'get'.ucfirst($field);
            $dbFields[$field] = method_exists($this, $method) ? $this->$method() : $this->$field;
        }
        return $dbFields;
    }

    public function setLoaded()
    {
        $this->isNewEntity = false;
    }

    public function load($data)
    {
        foreach ($data as $field=>$value) {
            $method = 'set'.ucfirst($field);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } elseif (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
        return true;
    }


    public function save()
    {
        return  ($this->isNew()) ? $this->insert() : $this->update();
    }

    protected function insert()
    {
        if (!$this->isNew()) {
            return false;
        }
        $fields = $this->getDbFields();
        $result = $this->insertRaw($fields);
        if ($result) {
            $this->setId($result);
        }
        $this->isNewEntity = false;
        return $result;
    }

    protected function update()
    {
        $id = $this->getId();
        if ($this->isNew() || empty($id)) {
            return false;
        }
        return $this->updateRaw($this->getId(), $this->getDbFields());
    }

    public function delete()
    {
        if ($this->isNew()) {
            return true;
        }
        return $this->deleteRaw($this->getId());
    }

    public function toArray()
    {
        $fields = $this->getDbFields();
        $fields['id'] = $this->getId();
        return $fields;
    }


}