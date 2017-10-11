<?php


namespace Akademiano\EntityOperator\Worker;


interface KeeperInterface
{
    const WORKER_INFO_TABLE = 'table';
    const WORKER_INFO_FIELDS = 'fields';

    public function get($id);

    public function save(array $data, $isExisting);

    public function delete($id);
}
