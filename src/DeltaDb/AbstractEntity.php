<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


abstract class AbstractEntity implements EntityInterface, \JsonSerializable
{
    protected $id;
    protected $repository;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFieldsList()
    {
        $methods = get_class_methods($this);
        $fields = [];
        foreach($methods as $method) {
            if ($pos = strpos($method, "get") !== false) {
                $field = substr($method, $pos);
                $fields[] = $field;
            }
        }
        return $fields;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {

    }

    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function toArray()
    {
        $result = [];
        /** @var Repository $repository */
        $repository = $this->getRepository();
        $fields = $repository->getFieldsList($repository->getTableName($this));
        foreach ($fields as $field) {
            $value = $repository->getField($this, $field);
            if ($value instanceof AbstractEntity) {
                $value = $value->toArray();
            }
            $result[$field] = $value;
        }
        return $result;
    }

    public function setValue($values)
    {
        /** @var Repository $repository */
        $repository = $this->getRepository();
        foreach ($values as $field => $value) {
            $repository->setField($this, $field, $value);
        }
    }

}