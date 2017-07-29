<?php

namespace Akademiano\Attach\Model\Command;


use Attach\Model\FileEntity;
use DeltaPhp\Operator\Command\Command;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\EntityInterface;

class AddFileCommand extends Command implements CommandInterface
{
    const COMMAND_ADD_FILE = "add.file";

    public function __construct(FileEntity $file, EntityInterface $entity, $relationClass, $params)
    {
        $params["file"] = $file;
        $params["entity"] = $entity;
        parent::__construct($params, $relationClass, self::COMMAND_ADD_FILE);
    }
}
