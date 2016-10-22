<?php


namespace Attach\Model\Worker;

use Attach\Model\ImageFileEntity;
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Worker\KeeperInterface;
use DeltaPhp\Operator\Worker\FinderInterface;

class ImageAttachWorker extends FileAttachWorker implements WorkerInterface, KeeperInterface, FinderInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable("images");
        $this->addFields(['main', "order"]);
//        $this->addUnmergedFields();
    }

    protected static function getDefaultMetadata()
    {
        return [
            WorkerInterface::PARAM_TABLEID => 13
        ];
    }

    protected static function getDefaultMapping()
    {
        $map = parent::getDefaultMapping();
        $mapping = self::mergeMapping($map, ImageFileEntity::class);
        return $mapping;
    }
}
