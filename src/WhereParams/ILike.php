<?php

namespace Akademiano\Db\Adapter\WhereParams;


class ILike
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }
}
