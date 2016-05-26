<?php


namespace EntityOperator\Operator;


interface KeeperInterface
{
    public function get($class = null, $id);

    public function save($entity);

    public function delete($entity);

}
