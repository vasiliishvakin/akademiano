<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;
use EntityOperator\Entity\Entity;
use EntityOperator\Operator\CreatorInterface;

class EntityCreatorWorker implements WorkerInterface, CreatorInterface
{
    public function create($class = null)
    {
        if (null === $class) {
            $class = Entity::class;
        }

        if ($class[0] !== "\\") {
            $class = "\\" . $class;
        }
        return new $class();
    }

    public function execute(CommandInterface $command)
    {
        if ($command->getName() !== CommandInterface::COMMAND_CREATE) {
            throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
        return $this->create($command->getClass());
    }
}
