<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Entity\Entity;
use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class LoadCommand extends Command
{
    const COMMAND_NAME = "load";

    public function __construct(EntityInterface $entity, array $data)
    {
        parent::__construct(["entity" =>$entity, "data" => $data], null);
    }

    public function getClass()
    {
        if (null === $this->class) {
            $entity = $this->getParams("entity");
            $this->class = (null !== $entity && is_object($entity)) ? get_class($entity) : Entity::class;
        }
        return $this->class;
    }
}
