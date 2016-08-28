<?php


namespace DeltaDb\D2QL;


use DeltaDb\Adapter\PgsqlAdapter;

abstract class Element implements ElementInterface
{
    /** @var  PgsqlAdapter */
    protected $adapter;

    public function getAdapter()
    {
        return $this->adapter;
    }


    public function setAdapter(PgsqlAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function escapeIdentifier($string)
    {
        return $this->getAdapter()->escapeIdentifier($string);
    }

    public function escape($value)
    {
        return $this->getAdapter()->escape($value);
    }
}
