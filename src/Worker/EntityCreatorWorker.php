<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;
use EntityOperator\Entity\Entity;
use EntityOperator\Operator\CreatorInterface;
use EntityOperator\Operator\IncludeOperatorInterface;
use EntityOperator\Operator\IncludeOperatorTrait;

class EntityCreatorWorker implements WorkerInterface, CreatorInterface, IncludeOperatorInterface
{
    use IncludeOperatorTrait;

    public function create($class = null)
    {
        if (null === $class) {
            $class = Entity::class;
        }

        if ($class[0] !== "\\") {
            $class = "\\" . $class;
        }
        $entity = new $class();
        if ($entity instanceof IncludeOperatorInterface) {
            $entity->setOperator($this->getOperator());
        }
        return $entity;
    }

    public function execute(CommandInterface $command)
    {
        if ($command->getName() !== CommandInterface::COMMAND_CREATE) {
            throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
        return $this->create($command->getClass());
    }
}
