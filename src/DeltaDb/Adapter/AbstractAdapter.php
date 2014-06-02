<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Adapter;


abstract class AbstractAdapter implements AdapterInterface
{
    protected $connection;
    protected $dsn;
    protected $params = [];

    function __construct($dsn = null, $params = [])
    {
        if (!is_null($dsn)) {
            $this->setDsn($dsn);
        }
        if (!empty($params)) {
            $this->setParams($params);
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

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
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