<?php


namespace DeltaDb\D2QL;


use DeltaDb\Adapter\PgsqlAdapter;

class Where extends Element implements CriteriaInterface, WhereInterface
{
    use CriteriaRelationInclude;

    const TYPE_NORMAL = "normal";
    const TYPE_EXP = "exp";
    const TYPE_ID = "id";

    const REL_AND = "and";
    const REL_OR = "or";

    protected $table;

    protected $relatedTable;

    protected $field;

    protected $value;

    /** @var bool */
    //protected $trustedValue = false;

    protected $operator;

    protected $type = self::TYPE_NORMAL;


    public function __construct(PgsqlAdapter $adapter, $field, $value, $operator = "=", $table = null, $relation = self::REL_AND, $type = self::TYPE_NORMAL)
    {
        $this->setAdapter($adapter);
        $this->setTable($table);
        $this->setField($field);
        $this->setValue($value);
        $this->setOperator($operator);
        $this->setRelation($relation);
        $this->setType($type);
    }

    /**
     * @return mixed
     */
    public function getRelatedTable()
    {
        if (null === $this->relatedTable) {
            if ($this->getType() === self::TYPE_ID) {
                $value = explode(".", $this->getValue());
                if (count($value) === 2) {
                    $this->setRelatedTable($value[0]);
                }
            }
        }
        return $this->relatedTable;
    }

    /**
     * @param mixed $relatedTable
     */
    public function setRelatedTable($relatedTable)
    {
        $this->relatedTable = $relatedTable;
    }


    /**
     * @return mixed
     */
    public function getTable()
    {

        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
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
     * @return boolean
     */
    /* public function isTrustedValue()
     {
         return $this->trustedValue;
     }*/

    /**
     * @param boolean $trustedValue
     */
    /*public function setTrustedValue($trustedValue)
    {
        $this->trustedValue = $trustedValue;
    }*/

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

    public function toSql()
    {
        $field = ((!empty($this->getTable())) ? $this->escapeIdentifier($this->getTable()) . "." : "") . $this->escapeIdentifier($this->getField());
        switch ($this->getType()) {
            case self::TYPE_NORMAL: {
                $value = $this->escape($this->getValue());
                break;
            }
            case self::TYPE_ID : {
                $value = $this->escapeIdentifier($this->getValue());
                break;
            }
            case self::TYPE_EXP : {
                $value = $this->getValue();
                break;
            }

        }
        $sql = $field . " " . $this->getOperator() . " " . $value;

        return $sql;
    }
}
