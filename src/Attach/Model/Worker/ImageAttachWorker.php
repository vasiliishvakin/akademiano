<?php


namespace Attach\Model\Worker;

use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Worker\KeeperInterface;
use DeltaPhp\Operator\Worker\FinderInterface;

class ImageAttachWorker extends FileAttachWorker implements WorkerInterface, KeeperInterface, FinderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("images");
    }
}
