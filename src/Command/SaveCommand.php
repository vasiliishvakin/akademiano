<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\EntityInterface;

class SaveCommand extends Command
{
    protected $name = self::COMMAND_SAVE;

    public function __construct(EntityInterface $entity = null, $params = [])
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class);
    }
}
