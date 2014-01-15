<?php

namespace DeltaDb;
use DeltaUtils\Parts\SetParams;

/**
 * Class AbstractDbTable
 */
class Table
{
    use SetParams;

    protected $dbaName;
    protected $tableName;

    /**
     * @param array $params ['dbaName' = null]
     */
    function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * @param mixed $dbaName
     */
    public function setDbaName($dbaName)
    {
        $this->dbaName = $dbaName;
    }

    /**
     * @return mixed
     */
    public function getDbaName()
    {
        return $this->dbaName;
    }

    /**
     * @param mixed $table
     */
    public function setTableName($table)
    {
        $this->tableName = $table;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return \DbSimple_Generic
     */
    public function getDba()
    {
        return DbaStorage::getDba($this->getDbaName());
    }

    public function insertRaw($data)
    {
        $dba = $this->getDba();
        return $dba->query("insert into {$this->getTableName()} (?#) VALUES(?a)", array_keys($data), array_values($data));
    }

    public function updateRaw($id, $data)
    {
        $dba = $this->getDba();
        return $dba->query("UPDATE {$this->getTableName()} SET ?a where id=?", $data, $id);
    }

    public function insertOrUpdateRaw($data)
    {
        $dba = $this->getDba();
        return $dba->query("insert into {$this->getTableName()} (?#) VALUES(?a) ON DUPLICATE KEY UPDATE ?a",
            array_keys($data), array_values($data), $data);
    }

    public function deleteRaw($value, $field = "id")
    {
        $dba = $this->getDba();
        return $dba->query("delete from {$this->getTableName()} where ?#=?", $field, $value);
    }

    public function getByIdRaw($id, $idField = "id")
    {
        $dba = $this->getDba();
        return $dba->selectRow("select * from {$this->getTableName()} where ?#=?", $idField, $id);
    }

    public function getAllRaw()
    {
        $dba = $this->getDba();
        return $dba->select("select * from {$this->getTableName()}");
    }

    public function find($criteria)
    {
        $dba = $this->getDba();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select * from {$this->getTableName()} {$where}";
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dba, 'select'], $selectParams);
        return $result;
    }

    public function findCol($colName, $criteria)
    {
        $dba = $this->getDba();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select ?# from {$this->getTableName()} {$where}";
        $selectParams[] = $colName;
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dba, 'selectCol'], $selectParams);
        return $result;
    }

    public function findCell($cellName, $criteria)
    {
        $dba = $this->getDba();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select ?# from {$this->getTableName()} {$where}";
        $selectParams[] = $cellName;
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dba, 'selectCell'], $selectParams);
        return $result;
    }

}
