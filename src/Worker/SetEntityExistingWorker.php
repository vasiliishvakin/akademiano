<?php


namespace Akademiano\EntityOperator\Worker;

use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Entity\EntityInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommand;
use Akademiano\Utils\Object\Collection;

class SetEntityExistingWorker implements WorkerInterface
{
    use WorkerMetaMapPropertiesTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case AfterCommandInterface::PREFIX_COMMAND_AFTER . FindCommand::COMMAND_NAME :
            case AfterCommandInterface::PREFIX_COMMAND_AFTER . GetCommand::COMMAND_NAME :
                /** @var AfterCommandInterface $command */
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
