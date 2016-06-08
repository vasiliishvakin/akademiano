<?php


namespace Attach\Model\Command;


use DeltaPhp\Operator\Command\Command;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Entity\RelationEntity;
use HttpWarp\Request;

class EntityAttachSaveCommand extends Command implements CommandInterface
{
    const COMMAND_ATTACH_SAVE = "attach.save";

    public function __construct(EntityInterface $entity, Request $request, $relationClass = RelationEntity::class, $params = [])
    {
        $params["entity"] = $entity;
        $params["request"] = $request;

        parent::__construct($params, $relationClass, self::COMMAND_ATTACH_SAVE);
    }
}
