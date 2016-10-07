<?php

namespace DeltaDb\D2QL\Prototype;

use DeltaDb\D2QL\Where;

class WherePrototype
{
    protected $table;
    protected $field;
    protected $value;
    protected $type;
    protected $operator;

    /**
     * WherePrototype constructor.
     * @param $table
     * @param string $field
     * @param $value
     * @param string $type
     */
    public function __construct($value, $field = "id", $operator = "=", $type = Where::TYPE_NORMAL, $table=null)
    {
        $this->setTable($table);
        $this->setField($field);
        $this->setType($type);
        $this->setOperator($operator);
        $this->setValue($value);
    }

    /**
     * @return mixed$t
$f
ty
$v
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
}
