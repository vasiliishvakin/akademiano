<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Entity\EntityInterface;

interface KeeperInterface
{
    public function get($id);

    public function save(array $data, $isExisting);

    public function delete($id);
}
