<?php

/**
 * Class ModelDbBase
 */
abstract class ModelDbBase
{

    abstract public function getTable();

    public function getDao()
    {
        throw new LogicException('not implemented');
    }

    public function insertRaw($data)
    {
        $dao = $this->getDao();
        return $dao->query("insert into {$this->getTable()} (?#) VALUES(?a)", array_keys($data), array_values($data));
    }

    public function updateRaw($id, $data)
    {
        $dao = $this->getDao();
        return $dao->query("UPDATE {$this->getTable()} SET ?a where id=?", $data, $id);
    }

    public function insertOrUpdateRaw($data)
    {
        $dao = $this->getDao();
        return $dao->query("insert into {$this->getTable()} (?#) VALUES(?a) ON DUPLICATE KEY UPDATE ?a",
            array_keys($data), array_values($data), $data);
    }

    public function deleteRaw($value, $field = "id")
    {
        $dao = $this->getDao();
        return $dao->query("delete from {$this->getTable()} where ?#=?", $field, $value);
    }

    public function getByIdRaw($id, $idField = "id")
    {
        $dao = $this->getDao();
        return $dao->selectRow("select * from {$this->getTable()} where ?#=?", $idField, $id);
    }

    public function getAllRaw()
    {
        $dao = $this->getDao();
        return $dao->select("select * from {$this->getTable()}");
    }

    public function find($criteria)
    {
        $dao = $this->getDao();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select * from {$this->getTable()} {$where}";
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dao, 'select'], $selectParams);
        return $result;
    }

    public function findCol($colName, $criteria)
    {
        $dao = $this->getDao();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select ?# from {$this->getTable()} {$where}";
        $selectParams[] = $colName;
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dao, 'selectCol'], $selectParams);
        return $result;
    }

    public function findCell($cellName, $criteria)
    {
        $dao = $this->getDao();
        $whereKeys = array_keys($criteria);
        $where = (count($whereKeys)>0) ? ' where ' : '';
        $first = true;
        foreach ($whereKeys as $field) {
            $where .= ($first) ? '' : ' and ';
            $where .= " {$field}=? ";
            $first = false;
        }
        $selectParams[] = "select ?# from {$this->getTable()} {$where}";
        $selectParams[] = $cellName;
        $values = array_values($criteria);
        foreach ($values as $value) {
            $selectParams[] = $value;
        }
        $result = call_user_func_array([$dao, 'selectCell'], $selectParams);
        return $result;
    }

}
