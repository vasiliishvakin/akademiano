<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Adapter;


abstract class AbstractAdapter implements AdapterInterface
{
    protected $connection;
    protected $dsn;

    function __construct($dsn = null)
    {
        if (!is_null($dsn)) {
            $this->setDsn($dsn);
        }
    }

    /**
     * @param mixed $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    public function getDsn()
    {
        return $this->dsn;
    }

    public function IsConnect()
    {
        return !is_null($this->connection);
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

} 