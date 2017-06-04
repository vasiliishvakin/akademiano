<?php


namespace Akademiano\Db\Adapter\D2QL;

class Select extends Query implements SelectInterface
{
    use CriteriaInclude;

    /**
     * @var bool
     */
    protected $distinct = false;

    protected $tables = [];

    protected $fields = [];

    protected $order = [];

    protected $limit;

    protected $offset;

    /**
     * @return boolean
     */
    public function isDistinct()
    {
        return $this->distinct;
    }

    /**
     * @param boolean $distinct
     */
    public function setDistinct($distinct)
    {
        $this->distinct = $distinct;
    }

    public function getDistinct()
    {
        return ($this->isDistinct()) ? "distinct" : "";
    }

    public function addTable($table)
    {
        if (!isset($this->tables[$table])) {
            $this->tables[$table] = $table;
        }
        return $this;
    }

    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function addField($field, $table = null, $isExpression = false)
    {
        if (null !== $table) {
            $this->addTable($table);
        }
        $this->fields[$table][$field] = ["field" => $field, "isExpression" => $isExpression];
        return $this;
    }

    /**
     * @param array $fields ["table_1"=>[field_1, field_2, ..., field_n], ..., "table_n"=>[field_1, ..., field_n]]
     * @return  Select;
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $table => $tableFields) {
            foreach ($tableFields as $field) {
                $this->addField($field, $table);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param array $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return array
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param array $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getSqlFields()
    {
        $fieldsSql = [];
        $tables = $this->getTables();
        foreach ($tables as $table) {
            if (!empty($table)) {
                break;
            }
        }
        if (empty($table)) {
            throw new \RuntimeException("empty tables");
        } else {
            $defaultTable = $table;
        }
        foreach ($this->fields as $table => $fields) {
            if (empty($table)) {
                $table = $defaultTable;
            }
            foreach ($fields as $fieldData) {
                if ($fieldData["isExpression"]) {
                    $field = str_replace("__TABLE__", $table, $fieldData["field"]);
                    $fieldsSql[] = $field;
                } else {
                    $fieldsSql[] = $this->escapeIdentifier($table) . "." . $this->escapeIdentifier($fieldData["field"]);
                }
            }
        }
        $fieldsSql = implode(", ", $fieldsSql);
        return $fieldsSql;
    }

    public function getSqlStart()
    {
        return "select";
    }

    public function getSqlFrom()
    {
        $tables = $this->getTables();
        $criteriaTables = $this->getCriteria()->getCriteriaTables();

        $tables = array_merge($tables, $criteriaTables);
        $tables = array_unique($tables);

        $adapter = $this->getAdapter();
        $tables = array_map(function ($value) use ($adapter) {
            return $adapter->escapeIdentifier($value);
        }, $tables);
        $tables = implode(", ", $tables);
        return $tables;
    }

    public function getSqlJoin()
    {
        return $this->getCriteria()->getSqlJoin();
    }


    public function toSql()
    {
        $where = $this->getSqlWhere();
        $where = ("" === $where) ? "" : "where " . $where;

        $sql = $this->getSqlStart() . " " . $this->getDistinct() . " " . $this->getSqlFields() . " from " . $this->getSqlFrom() . " " . $this->getSqlJoin()
            . " " . $where;

        return $sql;
    }
}
