<?php


namespace Akademiano\EntityOperator\Worker;


interface KeeperInterface
{
    public function get($id);

    public function save(array $data, $isExisting);

    public function delete($id);
}
