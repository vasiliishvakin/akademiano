<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\EntityInterface;

class ReserveCommand extends Command implements ReserveCommandInterface
{
    public function __construct(EntityInterface $entity)
    {
        $params["entity"] = $entity;
        $class = get_class($entity);
        parent::__construct($params, $class, self::COMMAND_RESERVE);
    }
}
