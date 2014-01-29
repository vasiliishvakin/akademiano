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
            $result = pg_query_params($query, $params);
        }
        return $result;
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

    public function insert($table, $fields)
    {
        // TODO: Implement insert() method.
    }

    public function getWhere(array $criteria)
    {

    }

    public function update($table, $fields, array $criteria)
    {
        // TODO: Implement update() method.
    }

    public function delete($table, array $criteria)
    {
        // TODO: Implement delete() method.
    }

    public function selectBy($table, array $criteria = [])
    {
        // TODO: Implement selectBy() method.
    }


} 