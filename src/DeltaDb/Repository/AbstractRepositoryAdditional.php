<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Repository;

abstract class AbstractRepositoryAdditional extends AbstractRepository
{
    protected $repository;

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return call_user_func($this->getClass() . '::Repository');
    }

    public function getTableName()
    {
        if (is_null($this->tableName)) {
            $this->tableName = $this->getRepository()->getTableName();
        }
        return $this->tableName;
    }

}