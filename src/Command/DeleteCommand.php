<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\EntityInterface;

class DeleteCommand extends Command
{
    protected $name = self::COMMAND_DELETE;

    public function __construct(EntityInterface $entity, array $params = null)
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class, self::COMMAND_DELETE);
    }

}
