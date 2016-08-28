<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


use DeltaCore\Prototype\AbstractEntity;
use DeltaCore\Prototype\MagicMethodInterface;
use DeltaUtils\StringUtils;

class VariableEntity extends AbstractEntity implements EntityInterface, MagicMethodInterface
{
    protected $fields = [];

    public function setFieldList(array $fields)
    {
        $newKeys = array_diff_key($fields, $this->getFieldsList());
        $newArray = array_fill_keys($newKeys, null);
        $newFields = array_keys(array_merge($this->fields, $newArray));
        $prepareFields = [];
        foreach ($newFields as $key => $field) {
            if (strpos($field, "_")) {
                $fieldParts = explode("_", $field);
                foreach ($fieldParts as $key => $part) {
                    if ($key === 0) {
                        continue;
                    }
                    $fieldParts[$key] = ucfirst($part);
                }
                $field = implode("", $fieldParts);
            }
            $prepareFields[$field] = null;
        }
        $this->fields = $prepareFields;
    }

    public function getFieldsList()
    {
        return array_keys($this->fields);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isFieldExist($name)
    {
        return $this->__isset($name);
    }

    public function setField($field, $value)
    {
        $field = StringUtils::lowDashToCamelCase($field);
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("Field $field not exist");
        }
        $this->fields[$field] = $value;
    }

    public function getField($field)
    {
        $field = StringUtils::lowDashToCamelCase($field);
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("Field $field not exist");
        }
        return $this->fields[$field];
    }

    public function __call($name, $arguments)
    {
        try {
            $result = parent::__call($name, $arguments);
            return $result;
        } catch (\BadMethodCallException $e) {
        }

        $prefix = lcfirst(substr($name, 0, 3));
        $field = lcfirst(substr($name, 3));
        if (($prefix !== "get" && $prefix !== "set") || !$this->isFieldExist($field)) {
            throw new \BadMethodCallException("method $name not exist");
        }
        switch ($prefix) {
            case "set":
                if (count($arguments) !== 1) {
                    throw new \InvalidArgumentException("In set field method you mast set value");
                }
                return $this->setField($field, $arguments[0]);
                break;
            case "get" :
                return $this->getField($field);
                break;
        }
    }

    public function __get($field)
    {
        $field = StringUtils::lowDashToCamelCase($field);
        if (!$this->isFieldExist($field)) {
            throw new \BadMethodCallException("field $field not exist");
        }
        return $this->getField($field);
    }

    public function __isset($field)
    {
        $field = StringUtils::lowDashToCamelCase($field);
        $fieldList = array_flip($this->getFieldsList());
        return isset($fieldList[$field]);
    }

    public function getId()
    {
        if (!$this->isFieldExist("id")) {
            throw new \BadMethodCallException("id field not defined");
        }
        return $this->getField("id");
    }
}
