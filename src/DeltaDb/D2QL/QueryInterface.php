<?php


namespace DeltaDb\D2QL;


interface QueryInterface
{
    /**
     * @return \DeltaDb\Adapter\PgsqlAdapter
     */
    public function getAdapter();
    public function escapeIdentifier($identifier);
    public function escape($value);
    public function toSql();
}
