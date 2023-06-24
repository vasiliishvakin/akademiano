<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
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
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;

class TranslatorDataToObjectEntityWorker implements EntityWorkerInterface, DelegatingInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    public const WORKER_ID = 'translatorDataToObjectEntityWorker';
    public const ENTITY = PostgresEntityWorker::ENTITY;

    use DelegatingTrait;
    use WorkerSelfInstanceTrait;
    use WorkerMappingTrait;

    public static function getSupportedCommands(): array
    {
        return [
            AfterCommand::class
        ];
    }

    public static function getEntityClassForMapFilter()
    {
        return static::ENTITY;
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch (true) {
            case is_subclass_of($command, AfterCommandInterface::class):
                return [
                    EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS => [
                        FilterFieldInterface::PARAM_ASSERTION => static::getEntityClassForMapFilter(),
                        FilterFieldInterface::PARAM_EXTRACTOR => ParentCommandEntityClassValueExtractor::class,
                    ],
                    SubCommandInterface::PARAM_PARENT_COMMAND => [
                        FilterFieldInterface::PARAM_ASSERTION => [FindCommand::class, GetCommand::class],
                        FilterFieldInterface::PARAM_EXTRACTOR => ValueClassExtractor::class,
                    ],
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
                    $result = $this->translate($command);
                    $command->addResult($result);
                    return $result;
                default:
                    throw new NotSupportedCommandException($command);
            }
        } else {
            throw new NotSupportedCommandException($command);
        }
    }

    public function translate(AfterCommandInterface $command)
    {
        $result = $command->extractResult();
        if (null === $result) {
            return null;
        }
        /** @var EntityCommandInterface $parentCommand */
        $parentCommand = $command->getParentCommand();
        $class = $parentCommand->getEntityClass();
        if ($result instanceof Collection) {
//            $items = clone $result;
            $items = $result->map(function ($itemData) use ($class) {
                if ($itemData instanceof EntityInterface) {
                    return $itemData;
                }
                $entity = $this->toEntity($itemData, $class);
                return $entity;
            });
            return $items;
        } else {
            return $this->toEntity($result, $class);
        }
    }

    public function toEntity(array $entityData, $entityClass)
    {
        if (empty($entityData)) {
            return null;
        }
        $createCommand = new CreateCommand($entityClass);

        $entity = $this->delegate($createCommand, true);

        $loadCommand = (new LoadCommand($entity))->setData($entityData);
        $entity = $this->delegate($loadCommand);
        return $entity;
    }
}
