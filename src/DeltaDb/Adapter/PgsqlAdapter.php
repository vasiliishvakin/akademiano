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
        return pg_fetch_all($result);
    }

    public function selectRow($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_row($result);
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

    public function getWhere(array $criteria, $num = 0)
    {
        $where = array_keys($criteria);
        $where = array_map(function ($value) use($num) {
            $num ++;
            return pg_escape_identifier($value) . '= $' . $num;
        }, $where);
        $where = implode(' and ', $where);
        if (!empty($where)){
            $where = ' where ' . $where;
        }
        return $where;
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
        $whereParams = array_values($criteria);
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
        $whereParams = array_values($criteria);
        $result = $this->queryParams($query, $whereParams);

        return ($result === false) ? false : pg_affected_rows($result);
    }

    public function selectBy($table, array $criteria = [])
    {
        $query = "select * from {$table}";
        if (empty($criteria)) {
            return $this->select($query);
        }
        $query .= $this->getWhere($criteria);
        $whereParams = array_values($criteria);
        array_unshift($whereParams, $query);
        return call_user_func_array([$this, 'select'],  $whereParams);
    }


} 