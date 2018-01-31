<?php


namespace Akademiano\EntityOperator\Worker;

use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\WorkersMap\Filter\ParentCommandEntityClassValueExtractor;
use Akademiano\Operator\Command\AfterCommand;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\Operator\WorkersMap\Filter\ValueClassExtractor;
use Akademiano\Utils\Object\Collection;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;

class SetEntityExistingEntityWorker implements EntityWorkerInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    const WORKER_ID = 'setEntityExistingEntityWorker';

    use WorkerSelfInstanceTrait;
    use WorkerMappingTrait;

    public static function getSupportedCommands(): array
    {
        return [
            AfterCommand::class
        ];
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch (true) {
            case is_subclass_of($command, AfterCommandInterface::class):
                return [
                    SubCommandInterface::PARAM_PARENT_COMMAND => [
                        [
                            FilterFieldInterface::PARAM_ASSERTION => [FindCommand::class, GetCommand::class],
                            FilterFieldInterface::PARAM_EXTRACTOR => ValueClassExtractor::class,
                        ],
                        [
                            FilterFieldInterface::PARAM_ASSERTION => Entity::class,
                            FilterFieldInterface::PARAM_EXTRACTOR => ParentCommandEntityClassValueExtractor::class
                        ]
                    ]
                ];
            default:
                return null;
        }
    }

    public function execute(CommandInterface $command)
    {
        if ($command instanceof AfterCommandInterface) {
            $parentCommand = $command->getParentCommand();
            switch (true) {
                case $parentCommand instanceof FindCommand :
                case $parentCommand instanceof GetCommand :
                    /** @var AfterCommandInterface $command */
                    $result = $this->set($command);
                    $command->addResult($result);
                    return $result;
                default:
                    throw new NotSupportedCommandException($command);
            }
        } else {
            throw new NotSupportedCommandException($command);
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
