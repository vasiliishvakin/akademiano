<?php


namespace Attach\Model\Worker;


use DeltaPhp\Operator\Worker\PostgresWorker;
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Worker\KeeperInterface;
use DeltaPhp\Operator\Worker\FinderInterface;

class FileAttachWorker extends PostgresWorker implements WorkerInterface, KeeperInterface, FinderInterface
{
    public function __construct()
    {
        $this->setTable("files");
        $this->addFields(["type", "sub_type", "path"]);
    }
}
