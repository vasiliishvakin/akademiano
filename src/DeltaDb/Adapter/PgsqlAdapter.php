<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Adapter;


class PgsqlAdapter extends AbstractAdapter
{
    protected $isTransaction = 0;

    public function connect($dsn = null)
    {
        if (!is_null($dsn)) {
            $this->setDsn($dsn);
        }
        $connection = pg_connect($this->getDsn());
        $this->setConnection($connection);
    }

    public function select($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        $rows = pg_fetch_all($result);
        if(!is_array($rows)) {
            return [];
        }
        return $rows;
    }

    public function selectRow($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_row($result, 0, PGSQL_ASSOC);
    }

    public function selectCol($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_all_columns($result, 0);
    }

    public function selectCell($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_result($result, 0,0);
    }

    public function query($query)
    {
        $connection = $this->getConnection();
        if (func_num_args() === 1) {
            $result = pg_query($connection, $query);
        } else {
            $params = func_get_args();
            array_shift($params);
            $result = $this->queryParams($query, $params);
        }
        return $result;
    }

    public function queryParams($query, $params)
    {
        return pg_query_params($query, $params);
    }

    /**
     * @param int $isTransaction
     */
    protected function setIsTransaction($isTransaction)
    {
        $this->isTransaction = $isTransaction;
    }

    /**
     * @return int
     */
    public function IsTransaction()
    {
        return $this->isTransaction;
    }

    public function begin()
    {
        if ($this->isTransaction()) {
            throw new \LogicException('Transaction already started');
        }
        $this->setIsTransaction(true);
        pg_query('BEGIN');
    }

    public function commit()
    {
        if ($this->isTransaction()) {
            throw new \LogicException('Transaction not started');
        }
        pg_query('COMMIT');
        $this->setIsTransaction(false);
    }

    public function rollBack()
    {
        if ($this->isTransaction()) {
            throw new \LogicException('Transaction not started');
        }
        pg_query('ROLLBACK');
        $this->setIsTransaction(false);
    }

    public function insert($table, $fields, $idName = null)
    {
        $fieldsList = $placeholders = array_keys($fields);
        $fieldsList = implode(', ', $fieldsList);
        $num = 0;
        $placeholders = array_map(function ($value) use(&$num) {
            $num ++;
            return '$' . $num;
        }, $placeholders);
        $placeholders = implode(', ', $placeholders);
        if (!is_null($idName)) {
            $idName = "returning {$idName}";
        }

        $query = "insert into {$table} ({$fieldsList}) values({$placeholders}) {$idName}";
        $result = $this->queryParams($query, $fields);
        if ($result === false) {
            return false;
        }
        if (is_null($idName)) {
            return (pg_affected_rows($result) >0);
        } else {
            return pg_fetch_result($result, 0, 0);
        }
    }

    public function escapeIdentifier($field)
    {
        if (strpos($field, ".") === false) {
            return pg_escape_identifier($field);
        }
        $fieldArr = explode(".", $field);
        return $fieldArr[0] . "." . pg_escape_identifier($fieldArr[1]);
    }

    public function getWhere(array $criteria, $num = 0)
    {
        $where = [];
        foreach ($criteria as $field => $value) {
            if (!is_array($value)) {
                $num++;
                $where[] = $this->escapeIdentifier($field) . '=$' . $num;
            } else {
                $inParams = [];
                foreach($value as $valueItem){
                    $num++;
                    $inParams[] = "\${$num}";
                }
                $inParams = implode(', ', $inParams);
                $where[] = $this->escapeIdentifier($field) . " in ({$inParams})";
            }
        }
        $where = implode(' and ', $where);
        if (!empty($where)){
            $where = ' where ' . $where;
        }
        return $where;
    }

    public function getWhereParams(array $criteria)
    {
        $whereParams = [];
        foreach($criteria as $field=>$value) {
            if (!is_array($value)) {
                $whereParams[] = $value;
            } else {
                foreach($value as $valueItem) {
                    $whereParams[] = $valueItem;
                }
            }
        }
        return $whereParams;
    }

    public function getOrderBy($orderBy)
    {
        $orderStr = "";
        if (!is_null($orderBy)) {
            if (is_array($orderBy)) {
                $orderField = $orderBy[0];
                $orderDirect = $orderBy[1];
                $orderStr = " order by {$orderField} {$orderDirect}";
            } else {
                $orderStr = " order by {$orderBy}";
            }
        }
        return $orderStr;
    }


    public function update($table, $fields, array $criteria)
    {
        $query = "update {$table}";
        if (empty($criteria) || empty($fields)) {
            return false;
        }
        $fieldsNames = array_keys($fields);
        $num = 0;
        $fieldsNames = array_map(function ($value) use(&$num) {
            $num ++;
            return pg_escape_identifier($value) . '=$' . $num;
        }, $fieldsNames);
        $fieldsNames = ' set ' . implode(', ', $fieldsNames);
        $query .= $fieldsNames;
        $query .= $this->getWhere($criteria, $num);
        $fieldsValues = array_values($fields);
        $whereParams = $this->getWhereParams($criteria);
        $queryParam = array_merge($fieldsValues, $whereParams);
        $result = $this->queryParams($query, $queryParam);
        return ($result === false) ? false : pg_affected_rows($result);
    }

    public function delete($table, array $criteria)
    {
        $query = "delete from {$table}";
        if (empty($criteria)) {
            return false;
        }
        $query .= $this->getWhere($criteria);
        $whereParams = $this->getWhereParams($criteria);
        $result = $this->queryParams($query, $whereParams);

        return ($result === false) ? false : pg_affected_rows($result);
    }

    public function getLimit($limit = null, $offset = null)
    {
        $sql = "";
        if (!is_null($limit)) {
            $limit = (integer)$limit;
            $sql .= " limit {$limit}";
        }
        if (!is_null($offset)) {
            $offset = (integer) $offset;
            $sql .= " offset {$offset}";
        }
        return $sql;
    }

    public function selectBy($table, array $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $query = "select * from \"{$table}\"";
        $limitSql = $this->getLimit($limit, $offset);
        if (empty($criteria)) {
            $query .= $limitSql;
            return $this->select($query);
        }
        $orderStr = "";
        $query .= $this->getWhere($criteria);
        $query .= $orderStr;
        $query .= $limitSql;
        $whereParams = $this->getWhereParams($criteria);
        array_unshift($whereParams, $query);
        return call_user_func_array([$this, 'select'],  $whereParams);
    }

    public function count($table, array $criteria = [])
    {
        $query = "select count(*) from \"{$table}\"";
        if (empty($criteria)) {
            $result = $this->selectCell($query);
        } else {
            $query .= $this->getWhere($criteria);
            $whereParams = $this->getWhereParams($criteria);
            array_unshift($whereParams, $query);
            $result = call_user_func_array([$this, 'selectCell'], $whereParams);
        }
        return (integer)$result;
    }




} 