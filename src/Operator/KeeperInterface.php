<?php


namespace EntityOperator\Operator;


interface KeeperInterface
{
    public function get($id);

    public function save($entity);

    public function delete($entity);

}