<?php


namespace Akademiano\Attach\Model\Command;

use DeltaPhp\Operator\Command\Command;
use DeltaPhp\Operator\Command\CommandInterface;
use Attach\Model\FileEntity;
use DeltaPhp\Operator\Entity\EntityInterface;

class DeleteFileCommand extends Command implements CommandInterface
{
    const COMMAND_DELETE_FILE = "delete.file";

    public function __construct(FileEntity $file, EntityInterface $entity, $relationClass, $params)
    {
        $params["file"] = $file;
        $params["entity"] = $entity;
        parent::__construct($params, $relationClass, self::COMMAND_DELETE_FILE);
    }
}
