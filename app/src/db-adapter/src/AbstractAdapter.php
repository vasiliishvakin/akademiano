<?php

namespace Akademiano\Db\Adapter;


use Akademiano\Utils\ArrayTools;
use phpDocumentor\Reflection\Types\Callable_;

abstract class AbstractAdapter implements AdapterInterface
{
    const RESOURCE_ID = 'dbAdapter';
    const FILTER_VALUE_RESOURCE_ID = "filterValueToDbType";

    protected $connection;
    protected $dsn;
    protected $params = [];

    protected $filterValue;

    abstract public function escapeIdentifier($identifier);

    public function __construct($params = [])
    {
        if (!empty($params)) {
            $this->setParams($params);
        }
    }

    abstract public function connect();

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

    public function getDsnSafe()
    {
        $dsn = $this->getDsn();
        return preg_replace('/(password\s*=\s*)(\S+)/', "$1=*******", $dsn);
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

    public function isConnect()
    {
        return null === $this->connection;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * @return mixed
     */
    public function getFilterValue(): callable
    {
        if (!isset($this->filterValue)) {
            $this->filterValue = function ($value) {
                return $value;
            };
        }
        return $this->filterValue;
    }

    /**
     * @param mixed $filterValue
     */
    public function setFilterValue(callable $filterValue): void
    {
        $this->filterValue = $filterValue;
    }

    public function filterValue($value)
    {
        return $this->getFilterValue()($value);
    }

    public function getOrderBy($orderBy)
    {
        $orderStr = "";
        if (!is_null($orderBy)) {
            if (is_array($orderBy)) {
                if (ArrayTools::getArrayType($orderBy) === -1) {
                    $orderField = $orderBy[0];
                    $orderDirect = $orderBy[1];
                    $orderField = $this->escapeIdentifier($orderField);
                    $orderStr = " order by {$orderField} {$orderDirect}";
                } else {
                    $orderStr = [];
                    foreach ($orderBy as $key => $value) {
                        $key = $this->escapeIdentifier($key);
                        $orderStr [] = " {$key} {$value} ";
                    }
                    if (empty($orderStr)) {
                        return null;
                    }
                    $orderStr = " order by " . implode(",", $orderStr);
                }
            } else {
                $orderStr = " order by {$orderBy}";
            }
        }

        return $orderStr;
    }
}
