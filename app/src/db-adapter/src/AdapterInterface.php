<?php

namespace Akademiano\Db\Adapter;


interface AdapterInterface
{
    public function setDsn($dsn);

    public function connect();

    public function getConnection();

    public function select($query);

    public function selectRow($query);

    public function selectCol($query);

    public function selectCell($query);

    public function query($query);

    public function begin();

    public function commit();

    public function rollBack ();

    public function insert($table, $fields, $idName = null, $rawFields = null);

    public function update($table, $fields, array $criteria, $rawFields = null);

    public function delete($table, array $criteria);

    public function selectBy($table, array $criteria = [], $limit = null, $offset = null, $orderBy = null);

    public function getWhere(array $criteria, $num = 0);

    public function getWhereParams(array $criteria);

    public function getOrderBy($orderBy);

    public function getLimit($limit, $offset);

    public function count($table, array $criteria = []);
}
