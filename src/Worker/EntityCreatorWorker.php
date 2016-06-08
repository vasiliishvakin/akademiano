<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\Entity;
use DeltaPhp\Operator\CreatorInterface;
use DeltaPhp\Operator\IncludeOperatorInterface;
use DeltaPhp\Operator\IncludeOperatorTrait;

class EntityCreatorWorker implements WorkerInterface, CreatorInterface, IncludeOperatorInterface
{
    use IncludeOperatorTrait;

    public function create($class = null, array $params = [])
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
        return $this->create($command->getClass(), $command->getParams());
    }
}
