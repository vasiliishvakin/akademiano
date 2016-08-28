<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Parts;


use DeltaDb\Adapter\AdapterInterface;

trait DbaInclude
{
    /** @var  AdapterInterface */
    protected $dba;

    /**
     * @param \DeltaDb\Adapter\AdapterInterface $dao
     */
    public function setDba(AdapterInterface $dao)
    {
        $this->dba = $dao;
    }

    /**
     * @return \DeltaDb\Adapter\AdapterInterface
     */
    public function getDba()
    {
        return $this->dba;
    }

}

