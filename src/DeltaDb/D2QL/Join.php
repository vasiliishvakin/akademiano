<?php


namespace DeltaDb\D2QL;


use DeltaDb\Adapter\PgsqlAdapter;

class Join extends Element implements CriteriaInterface
{
    const TYPE_LEFT = "LEFT OUTER";
    const TYPE_RIGHT = "RIGHT OUTER ";
    const TYPE_INNER= "INNER";

    protected $table;

    protected $field;

    protected $relatedTable;

    protected $relatedField;

    protected $type = self::TYPE_LEFT;

    public function __construct(PgsqlAdapter $adapter, $table, $field, $relatedTable, $relatedField = "id", $type = self::TYPE_INNER)
    {
        $this->setAdapter($adapter);
        $this->setTable($table);
        $this->setField($field);
        $this->setRelatedTable($relatedTable);
        $this->setRelatedField($relatedField);
        $this->setType($type);
    }


    /**
     * @return mixed
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
    public function getRelatedTable()
    {
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
    public function getRelatedField()
    {
        return $this->relatedField;
    }

    /**
     * @param mixed $relatedField
     */
    public function setRelatedField($relatedField)
    {
        $this->relatedField = $relatedField;
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
        /*switch ($this->getType()) {
            case self::TYPE_LEFT :
                $joinType = "left";
                break;
            default:
                throw  new \LogicException("bad join type");

        }*/
        $joinType = $this->getType();
        $t1 = $this->escapeIdentifier($this->getTable());
        $t2 = $this->escapeIdentifier($this->getRelatedTable());
        $f1 = $this->escapeIdentifier($this->getField());
        $f2 = $this->escapeIdentifier($this->getRelatedField());

        $sql = "{$joinType} JOIN {$t1} ON {$t1}.{$f1}={$t2}.{$f2}";

        return $sql;
    }
}
