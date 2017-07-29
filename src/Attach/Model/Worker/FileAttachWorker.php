<?php


namespace Akademiano\Attach\Model\Worker;


use DeltaPhp\Operator\Worker\PostgresWorker;
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\Worker\KeeperInterface;
use DeltaPhp\Operator\Worker\FinderInterface;

class FileAttachWorker extends PostgresWorker implements WorkerInterface, KeeperInterface, FinderInterface
{
    public function __construct()
    {
        $this->setTable("files");
        $this->addFields(["title", "description", "type", "sub_type", "path"]);
        $this->addUnmergedFields(["type", "sub_type", "path"]);
    }

    protected static function getDefaultMetadata()
    {
        return [
            WorkerInterface::PARAM_TABLEID => 12
        ];
    }

    protected static function getDefaultMapping()
    {
        $map = parent::getDefaultMapping();
        $mapping = self::mergeMapping($map, FileEntity::class);
        $mapping = self::mergeMapping($mapping,
            [
                \Attach\Model\Command\AddFileCommand::COMMAND_ADD_FILE => null,
                \Attach\Model\Command\UpdateFileCommand::COMMAND_UPDATE_FILE => null,
                \Attach\Model\Command\DeleteFileCommand::COMMAND_DELETE_FILE => null,
            ]
        );
        return $mapping;
    }
}
