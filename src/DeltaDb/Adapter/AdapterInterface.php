<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Adapter;


interface AdapterInterface
{
    function __construct($dsn = null);

    public function connect($dsn = null);

    public function getConnection();

    public function select($query);

    public function selectRow($query);

    public function selectCol($query);

    public function selectCell($query);

    public function query($query);

    public function begin();

    public function commit();

    public function rollBack ();

    public function insert($table, $fields, $idName = null);

    public function update($table, $fields, array $criteria);

    public function delete($table, array $criteria);

    public function selectBy($table, array $criteria = []);

} 