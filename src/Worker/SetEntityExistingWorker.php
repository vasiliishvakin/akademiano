<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Worker\Exception\NotSupportedCommand;
use DeltaUtils\Object\Collection;

class SetEntityExistingWorker implements WorkerInterface
{
    use WorkerMetaMapPropertiesTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case AfterCommandInterface::COMMAND_AFTER_FIND :
            case AfterCommandInterface::COMMAND_AFTER_GET :
                $result = $this->set($command);
                $command->addResult($result);
                return $result;
            default:
                throw new NotSupportedCommand($command);
        }
    }

    public function set(AfterCommandInterface $command)
    {
        $result = $command->extractResult();
        if (null === $result) {
            return null;
        }
        if ($result instanceof Collection) {
            $items = clone $result;
            $items->map(function ($entity) {
                return $this->setEntityExisting($entity);
            });
            return $items;
        } else {
            return $this->setEntityExisting($result);
        }
    }

    public function setEntityExisting(EntityInterface $entity)
    {
        $entity->setExistingEntity(true);
        return $entity;
    }
}
