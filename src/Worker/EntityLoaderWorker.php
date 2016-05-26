<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;
use EntityOperator\Entity\EntityInterface;
use EntityOperator\Operator\LoaderInterface;

class EntityLoaderWorker implements WorkerInterface, LoaderInterface
{
    public function execute(CommandInterface $command)
    {
        if ($command->getName() !== CommandInterface::COMMAND_LOAD) {
            throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
        return $this->load($command->getParams("entity"), $command->getParams("data"));
    }

    public function load(EntityInterface $entity, array $data)
    {
        foreach ($data as $name => $value) {
            $method = "set" . ucfirst($name);
            if (method_exists($entity, $method)) {
                $entity->{$method}($value);
            }
        }
        return $entity;
    }
}
