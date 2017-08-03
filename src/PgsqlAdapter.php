<?php

namespace Akademiano\Db\Adapter;


use Akademiano\Db\Adapter\Exception\ConnectionErrorException;
use Akademiano\Db\Adapter\Type\PgPoint;
use Akademiano\Db\Adapter\WhereParams\Between;
use Akademiano\Db\Adapter\WhereParams\ILike;
use Akademiano\Entity\Uuid;
use Akademiano\Utils\CatchError;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\Utils\StringUtils;

class PgsqlAdapter extends AbstractAdapter
{
    protected $isTransaction = 0;

    public function connect()
    {
        CatchError::start();
        $connection = pg_connect($this->getDsn());
        $error = CatchError::stop();
        if ($error) {
            throw new ConnectionErrorException(
                sprintf('%s (use dsn: "%s")', $error->getMessage(), $this->getDsnSafe()),
                $error->getCode(),
                $error
            );
        }
        $this->connection = $connection;
    }

    public function query($query)
    {
        if (func_num_args() === 1) {
            $result = pg_query($this->getConnection(), $query);
        } else {
            $params = func_get_args();
            array_shift($params);
            $result = $this->queryParams($query, $params);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function isTransaction()
    {
        return $this->isTransaction;
    }

    public function begin()
    {
        if ($this->isTransaction()) {
            throw new \LogicException('Transaction already started');
        }
        $this->setTransaction(true);
        $this->query('BEGIN');
    }

    public function commit()
    {
        if (!$this->isTransaction()) {
            throw new \LogicException('Transaction not started');
        }
        $this->query('COMMIT');
        $this->setTransaction(false);
    }

    public function rollBack()
    {
        if (!$this->isTransaction()) {
            throw new \LogicException('Transaction not started');
        }
        $this->query('ROLLBACK');
        $this->setTransaction(false);
    }

    public function queryParams($query, $params)
    {
        return pg_query_params($this->getConnection(), $query, $params);
    }

    public function getError()
    {
        return pg_last_error($this->getConnection());
    }

    public function select($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        $rows = pg_fetch_all($result);
        if (!is_array($rows)) {
            return [];
        }
        return $rows;
    }

    public function selectAndSmartFetch($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        if (!$result) {
            throw new \RuntimeException("Bad query: \"$query\"");
        }
        $numRows = pg_num_rows($result);
        $numFields = pg_num_fields($result);
        if ($numRows === 0) {
            return null;
        }
        if ($numFields === 1) {
            $result = pg_fetch_all_columns($result);
            if ($numRows === 1) {
                return reset($result);
            }
            return $result;
        } else {
            if ($numRows === 1) {
                return pg_fetch_row($result);
            } else {
                return pg_fetch_all($result);
            }
        }
    }

    public function selectRow($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_row($result, 0, PGSQL_ASSOC);
    }

    public function selectCol($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_all_columns($result, 0);
    }

    public function selectCell($query)
    {
        $result = call_user_func_array([$this, 'query'], func_get_args());
        return pg_fetch_result($result, 0, 0);
    }

    /**
     * @param int $isTransaction
     */
    protected function setTransaction($isTransaction)
    {
        $this->isTransaction = $isTransaction;
    }

    public function insert($table, $fields, $idName = null, $rawFields = null)
    {
        $rawFields = array_flip((array)$rawFields);
        $fieldsList = array_keys($fields);
        $fieldsNames = $fieldsList;
        foreach ($fieldsList as $key => $value) {
            $nameParts = explode(".", $value);
            foreach ($nameParts as $keyPart => $keyValue) {
                $nameParts[$keyPart] = pg_escape_identifier($this->getConnection(), $keyValue);
            }
            $fieldsList[$key] = implode(".", $nameParts);
        }
        $fieldsList = implode(', ', $fieldsList);
        $num = 0;
        $fieldsQuery = [];
        foreach ($fieldsNames as $fieldName) {
            if (!isset($rawFields[$fieldName])) {
                $num++;
                $fieldsQuery[] = '$' . $num;
            } else {
                $fieldsQuery[] = $fields[$fieldName];
                unset($fields[$fieldName]);
            }
        }
        $fieldsQuery = implode(', ', $fieldsQuery);
        if (!is_null($idName)) {
            $idName = "returning {$idName}";
        }

        $query = "insert into {$table} ({$fieldsList}) values ({$fieldsQuery}) {$idName}";
        $result = $this->queryParams($query, $fields);
        if ($result === false) {
            return false;
        }
        if (is_null($idName)) {
            return (pg_affected_rows($result) > 0);
        } else {
            return pg_fetch_result($result, 0, 0);
        }
    }

    public function escapeIdentifier($identifier)
    {
        if (strpos($identifier, ".") === false) {
            return pg_escape_identifier($this->getConnection(), $identifier);
        }
        $fieldArr = explode(".", $identifier);
        return pg_escape_identifier($this->getConnection(), $fieldArr[0]) . "." . pg_escape_identifier($this->getConnection(), $fieldArr[1]);
    }

    public function escape($value)
    {
        return pg_escape_literal($this->getConnection(), $value);
    }

    public function getWhere(array $criteria, $num = 0)
    {
        $where = [];
        foreach ($criteria as $field => $value) {
            $isNot = (0 === strpos($field, "!")) ? true : false;
            if ($isNot) {
                $field = substr($field, 1);
            }
            if (is_object($value)) {
                switch (true) {
                    case $value instanceof Between :
                        /** @var Between $value */
                        $num++;
                        $num2 = $num + 1;
                        $where[] = $this->escapeIdentifier($field) . " between \${$num} and \${$num2}";
                        $num = $num2;
                        break;
                    case $value instanceof ILike:
                        /** @var ILike $value */
                        $num++;
                        $where[] = $this->escapeIdentifier($field) . " ILIKE \${$num}";
                        break;
                    case $value instanceof PgPoint:
                        $where[] = $this->escapeIdentifier($field) . "=" . $value->pgFormat();
                        break;
                    case $value instanceof IntegerableInterface:
                    case $value instanceof StringableInterface:
                        $num++;
                        $isNot = $isNot ? "!" : "";
                        $where[] = $this->escapeIdentifier($field) . "{$isNot}=$" . $num;
                        break;
                    default :
                        throw new \InvalidArgumentException(sprintf('Where class "%s" not implement', get_class($value)));
                }
            } elseif (is_array($value)) {
                $inParams = [];
                foreach ($value as $valueItem) {
                    $num++;
                    $inParams[] = "\${$num}";
                }
                $inParams = implode(', ', $inParams);
                $isNot = $isNot ? "not" : "";
                $where[] = $this->escapeIdentifier($field) . " {$isNot} in  ({$inParams})";
            } elseif (is_null($value)) {
                $isNot = $isNot ? "not" : "";
                $where[] = $this->escapeIdentifier($field) . " is {$isNot} null";
            } else {
                $num++;
                $isNot = $isNot ? "!" : "";
                $where[] = $this->escapeIdentifier($field) . "{$isNot}=$" . $num;
            }
        }
        $where = implode(' and ', $where);
        if (!empty($where)) {
            $where = ' where ' . $where;
        }
        return $where;
    }

    public function getWhereParams(array $criteria)
    {
        $whereParams = [];
        foreach ($criteria as $field => $value) {
            if (is_object($value)) {
                switch (true) {
                    case $value instanceof Between :
                        /** @var Between $value */
                        $whereParams[] = $value->getStart();
                        $whereParams[] = $value->getEnd();
                        break;
                    case $value instanceof ILike:
                        /** @var ILike $value */
                        $whereParams[] = $value->getQuery();
                        break;
                    case $value instanceof PgPoint:
                        break;
                    case $value instanceof IntegerableInterface:
                        $whereParams[] = $value->getInt();
                        break;
                    case $value instanceof StringableInterface:
                        $whereParams[] = (string)$value;
                        break;
                    default :
                        throw new \InvalidArgumentException(sprintf('Where class "%s" not implement', get_class($value)));
                }

            } elseif (is_array($value)) {
                foreach ($value as $valueItem) {
                    $whereParams[] = $valueItem;
                }
            } elseif (is_null($value)) {
                continue;
            } else {
                $whereParams[] = $value;
            }
        }
        return $whereParams;
    }

    public function update($table, $fields, array $criteria, $rawFields = null)
    {
        $query = "update {$table}";
        if (empty($criteria) || empty($fields)) {
            return false;
        }
        $rawFields = array_flip((array)$rawFields);
        $fieldsNames = array_keys($fields);
        $num = 0;
        $fieldsQuery = [];
        foreach ($fieldsNames as $fieldName) {
            if (!isset($rawFields[$fieldName])) {
                $num++;
                $fieldsQuery[] = $this->escapeIdentifier($fieldName) . '=$' . $num;
            } else {
                $fieldsQuery[] = $this->escapeIdentifier($fieldName) . '=' . $fields[$fieldName];
                unset($fields[$fieldName]);
            }
        }
        $fieldsQuery = ' set ' . implode(', ', $fieldsQuery);
        $query .= $fieldsQuery;
        $query .= $this->getWhere($criteria, $num);
        $fieldsValues = array_values($fields);
        $whereParams = $this->getWhereParams($criteria);
        $queryParam = array_merge($fieldsValues, $whereParams);
        $result = $this->queryParams($query, $queryParam);
        return ($result === false) ? false : pg_affected_rows($result);
    }

    public function delete($table, array $criteria)
    {
        $query = "delete from {$table}";
        if (empty($criteria)) {
            return false;
        }
        $query .= $this->getWhere($criteria);
        $whereParams = $this->getWhereParams($criteria);
        $result = $this->queryParams($query, $whereParams);

        return ($result === false) ? false : pg_affected_rows($result);
    }

    public function getLimit($limit = null, $offset = null)
    {
        $sql = "";
        if (!is_null($limit)) {
            $limit = (integer)$limit;
            $sql .= " limit {$limit}";
        }
        if (!is_null($offset)) {
            $offset = (integer)$offset;
            $sql .= " offset {$offset}";
        }
        return $sql;
    }

    public function selectBy($table, array $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $query = "select * from \"{$table}\"";
        $limitSql = $this->getLimit($limit, $offset);
        $orderStr = $this->getOrderBy($orderBy);
        $query .= $this->getWhere($criteria);
        $query .= $orderStr;
        $query .= $limitSql;
        $whereParams = $this->getWhereParams($criteria);
        array_unshift($whereParams, $query);
        return call_user_func_array([$this, 'select'], $whereParams);
    }

    public function count($table, array $criteria = [])
    {
        $query = "select count(*) from \"{$table}\"";
        if (empty($criteria)) {
            $result = $this->selectCell($query);
        } else {
            $query .= $this->getWhere($criteria);
            $whereParams = $this->getWhereParams($criteria);
            array_unshift($whereParams, $query);
            $result = call_user_func_array([$this, 'selectCell'], $whereParams);
        }
        return (integer)$result;
    }

    public function max($table, $field, $criteria = [])
    {
        $field = $this->escapeIdentifier($field);
        $query = "select max({$field}) from \"{$table}\"";
        if (empty($criteria)) {
            $result = $this->selectCell($query);
        } else {
            $query .= $this->getWhere($criteria);
            $whereParams = $this->getWhereParams($criteria);
            array_unshift($whereParams, $query);
            $result = call_user_func_array([$this, 'selectCell'], $whereParams);
        }
        return $result;
    }

    public function min($table, $field, $criteria = [])
    {
        $field = $this->escapeIdentifier($field);
        $query = "select min({$field}) from \"{$table}\"";
        if (empty($criteria)) {
            $result = $this->selectCell($query);
        } else {
            $query .= $this->getWhere($criteria);
            $whereParams = $this->getWhereParams($criteria);
            array_unshift($whereParams, $query);
            $result = call_user_func_array([$this, 'selectCell'], $whereParams);
        }
        return $result;
    }
}
