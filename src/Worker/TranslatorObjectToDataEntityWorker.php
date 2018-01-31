<?php


namespace Akademiano\EntityOperator\Worker;

use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\Operator\Command\PreCommand;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\EntityOperator\Command\ReserveCommand;
use Akademiano\Entity\EntityInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\Operator\WorkersMap\Filter\ValueClassExtractor;

class TranslatorObjectToDataEntityWorker implements EntityWorkerInterface, DelegatingInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    const WORKER_ID = 'translatorObjectToDataEntityWorker';

    use DelegatingTrait;
    use WorkerMappingTrait;
    use WorkerSelfInstanceTrait;

    public static function getSupportedCommands(): array
    {
        return [
            PreCommand::class,
        ];
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch (true) {
            case is_subclass_of($command, PreCommandInterface::class):
                return [
                    SubCommandInterface::PARAM_PARENT_COMMAND => [
                        FilterFieldInterface::PARAM_ASSERTION => [SaveCommand::class, DeleteCommand::class],
                        FilterFieldInterface::PARAM_EXTRACTOR => ValueClassExtractor::class,
                    ],
                ];
            default:
                return null;
        }
    }

    public function execute(CommandInterface $command)
    {

        if (!$command instanceof PreCommandInterface) {
            throw new NotSupportedCommandException($command);
        }

        $parentCommand = $command->getParentCommand();

        switch (true) {
            case $parentCommand instanceof SaveCommand:
                $entity = $parentCommand->getEntity();
                $data = $this->toData($entity);
                $parentCommand->setData($data);
                break;
            case $parentCommand instanceof DeleteCommand:
                $entity = $parentCommand->getEntity();
                $data = ["id" => $entity->getId()];
                $parentCommand->setData($data);
                break;
            default:
                throw new NotSupportedCommandException($parentCommand);
        }
    }

    public function toData(EntityInterface $entity)
    {
        $command = new ReserveCommand($entity);
        return $this->delegate($command);
    }
}
