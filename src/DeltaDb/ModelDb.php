<?php

abstract class ModelDb extends ModelDbBase
{
    // change in class
    protected static $table = 'undefined';
    protected static $modelClass = 'ModelDbExternal';
    protected static $modelTplClass = 'ModelTpl';

    //do not touch
    protected $isNewEntity = true;

    protected static $modelExternal;

    protected static $modelTpl;

    protected $id;

    protected $dbFieldsList;

    public function getExcludedFields()
    {
        return ['table', 'modelClass', 'modelTplClass', 'isNewEntity', 'modelExternal', 'modelTpl', 'id', 'dbFieldsList'];
    }

    /**
     * @return ModelDbExternal
     */
    public static function model()
    {
        if (is_null(static::$modelExternal)) {
            $model = static::$modelClass;
            static::$modelExternal = new $model(get_called_class(), static::$table);
        }
        return static::$modelExternal;
    }

    /**
     * @return ModelTpl
     */
    public static function modelTpl()
    {
        if (is_null(static::$modelTpl)) {
            $model = static::$modelTplClass;
            static::$modelTpl = new $model(get_called_class());
        }
        return static::$modelTpl;
    }

    public function getTable()
    {
        return self::model()->getTable();
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