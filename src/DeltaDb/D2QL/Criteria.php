<?php


namespace DeltaDb\D2QL;


use DeltaDb\Adapter\PgsqlAdapter;

class Criteria extends Element implements CriteriaIncludeInterface, ElementInterface
{
    /** @var CriteriaInterface[] */
    protected $criteria = [];


    public function __construct(PgsqlAdapter $adapter = null)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @return Where[]|WhereGroup[]
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    public function addCriteria(CriteriaInterface $criteria)
    {
        $this->criteria[] = $criteria;
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
        $criteria = new Where($this->getAdapter(), $table, $field, $value, $operator, $relation, $type);
        $this->addCriteria($criteria);
        return $this;
    }

    /**
     * @param string $relation
     * @return WhereGroup
     */
    public function createWhereGroup($relation = Where::REL_AND)
    {
        $whereGroup = new WhereGroup($this->getAdapter(), $relation);
        $this->addCriteria($whereGroup);
        return $whereGroup;
    }

    public function createJoin($table, $field, $relatedTable, $relatedField = "id", $type = Join::TYPE_INNER)
    {
        $join = new Join($this->getAdapter(), $table, $field, $relatedTable, $relatedField, $type);
        $this->addCriteria($join);
        return $this;
    }

    public function getCriteriaTables()
    {
        $criteria = $this->getCriteria();
        $tables = [];
        foreach ($criteria as $criteriaItem) {
            if ($criteriaItem instanceof CriteriaIncludeInterface) {
                $criteriaTables = $criteriaItem->getCriteriaTables();
                $tables = array_merge($tables, $criteriaTables);
            } elseif ($criteriaItem instanceof Join) {
                break;
            } else {
                $criteriaTable = $criteriaItem->getTable();
                if (!isset($tables[$criteriaTable])) {
                    $tables[$criteriaTable] = $criteriaTable;
                }
                $relatedTable = $criteriaItem->getRelatedTable();
                if (null !== $relatedTable && !isset($tables[$relatedTable])) {
                    $tables[$relatedTable] = $relatedTable;
                }
            }
        }
        return $tables;
    }

    public function getSqlJoin()
    {
        $joinSql = [];
        $criteria = $this->getCriteria();
        foreach ($criteria as $item) {
            if ($item instanceof Join) {
                $joinSql[] = $item->toSql();
            }
        }
        $joinSql = implode(" ", $joinSql);
        return $joinSql;
    }

    public function getSqlWhere()
    {
        $criteria = $this->getCriteria();
        $sql = "";
        foreach ($criteria as $criteriaItem) {
            if (($criteriaItem instanceof Join)) {
                continue;
            }
            $criteriaSql = $criteriaItem->toSql();
            if ("" !== $sql) {
                $relation = $criteriaItem->getRelation();
                $sql = $sql . " " . $relation;
            }
            $sql = $sql . " " . $criteriaSql;
        }
        return $sql;
    }
}
