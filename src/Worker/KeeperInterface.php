<?php


namespace DeltaPhp\Operator\Worker;


interface KeeperInterface
{
    public function get($id);

    public function save(array $data, $isExisting);

    public function delete($id);
}
