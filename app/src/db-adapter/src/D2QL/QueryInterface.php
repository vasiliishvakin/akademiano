<?php


namespace Akademiano\Db\Adapter\D2QL;


interface QueryInterface
{
    /**
     * @return \Akademiano\Db\Adapter\PgsqlAdapter
     */
    public function getAdapter();
    public function escapeIdentifier($identifier);
    public function escape($value);
    public function toSql();
}
