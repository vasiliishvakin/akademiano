<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


class VariableEntity extends AbstractEntity
{
    protected $fields = [];

    public function setFieldList(array $fields)
    {
        $newKeys = array_diff_key($fields, $this->getFieldsList());
        $newArray = array_fill_keys($newKeys, null);
        $newFields = array_merge($this->fields, $newArray);
        $this->fields = $newFields;
    } 
  
    public function getFieldsList()
    {
        return array_keys($this->fields);
    }

    public function isFieldExist($name)
    {
        $fieldList = array_flip($this->getFieldsList());
        return isset($fieldList[$name]);
    }

    public function setField($field, $value)
    {
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("Field $field not exist");
        }
        $this->fields[$field] = $value;
    }

    public function getField($field)
    {
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("Field $field not exist");
        }
        return $this->fields[$field];
    }

    function __call($name, $arguments)
    {
        $prefix = lcfirst(substr($name, 0, 3));
        $field = lcfirst(substr($name, 3));
        if ( ($prefix !== "get" && $prefix !== "set") || !$this->isFieldExist($field)) {
            throw new \BadMethodCallException("method $name not exist");
        }
        switch($prefix) {
            case "set":
                if (count($arguments)!==1) {
                    throw new \BadMethodCallException("In set field method you mast set value");
                }
                return $this->setField($field, $arguments[0]);
                break;
            case "get" :
                return $this->getField($field);
                break;
        }
    }

    function __get($field)
    {
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("field $field not exist");
        }
        return $this->getField($field);
    }

    function __isset($field)
    {
        return $this->isFieldExist($field);
    }

    public function getId()
    {
        if (!$this->isFieldExist("id")) {
            throw new \BadMethodCallException("id field not defined");
        }
        return $this->getField("id");
    }


} 