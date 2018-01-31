<?php


namespace Akademiano\EntityOperator\Worker;


interface KeeperInterface
{
    const PARAM_TABLEID = "tableId";

    public function get($id);

    public function save(array $data, $isExisting);

    public function delete($id);
}
