<?php


namespace DeltaDb\D2QL;

use DeltaDb\Adapter\PgsqlAdapter;

class WhereGroup extends Element implements CriteriaInterface, WhereInterface
{
    use CriteriaRelationInclude;
    use CriteriaInclude;

    public function __construct(PgsqlAdapter $adapter, $relation = Where::REL_AND)
    {
        $this->setAdapter($adapter);
        $this->setRelation($relation);
    }

    /**
     * @param $table
     * @param $field
     * @param $value
     * @param string $operator
     * @param string $relation
     * @param string $type
     * @return Select|WhereGroup
     */
    public function createWhere($table, $field, $value, $operator = "=", $relation = Where::REL_AND, $type = Where::TYPE_NORMAL)
    {
        $where = new Where($this->getAdapter(), $table, $field, $value, $operator, $relation, $type);
        $this->getCriteria()->addCriteria($where);
        return $this;
    }

    /**
     * @param string $relation
     * @return WhereGroup
     */
    public function createWhereGroup($relation = Where::REL_AND)
    {
        $whereGroup = new WhereGroup($this->getAdapter(), $relation);
        $this->getCriteria()->addCriteria($whereGroup);
        return $whereGroup;
    }

    public function toSql()
    {
        return "(" . $this->getSqlWhere() . ")";
    }

}
