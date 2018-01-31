<?php


namespace Akademiano\UUID\Worker;

use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\EntityOperator\WorkersMap\Filter\RelationCommandEntityClassValueExtractor;
use Akademiano\EntityOperator\Worker\EntityCreatorWorker;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\WorkersContainer;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\UUID\Command\UuidCreateCommand;
use Akademiano\UUID\Parts\UuidFactoryTrait;
use Akademiano\UUID\UuidComplexShort;
use Akademiano\UUID\UuidComplexShortTables;
use Akademiano\UUID\UuidFactory;

class UuidWorker extends EntityCreatorWorker
{
    const WORKER_ID = 'uuidWorker';

    use UuidFactoryTrait;
    use WorkerMappingTrait;
    use WorkerSelfInstanceTrait;

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        /** @var UuidWorker $instance */
        $instance = parent::getSelfInstance($container);
        $setUuidFactory = $container->getOperator()->getDependencies()[UuidFactory::RESOURCE_ID];
        $instance->setUuidFactory($setUuidFactory);
        return $instance;
    }

    public static function getSupportedCommands(): array
    {
        return [
            UuidCreateCommand::class,
        ];
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch ($command) {
            case UuidCreateCommand::class:
                return [
                    EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS => [
                        FilterFieldInterface::PARAM_ASSERTION => UuidComplexShort::class,
                        FilterFieldInterface::PARAM_EXTRACTOR => RelationCommandEntityClassValueExtractor::class,
                    ]
                ];
            default:
                return null;
        }
    }


    public function execute(CommandInterface $command)
    {
        switch (true) {
            case $command instanceof UuidCreateCommand:
                return $this->toUuid($command->getValue(), $command->getEntityClass(), $command->getEpoch());
            default:
                throw new NotSupportedCommandException($command);
        }
    }

    public function toUuid($value, $class = UuidComplexShortTables::class, $epoch = null)
    {
        return $this->getUuidFactory()->create($value, $epoch, $class);
    }
}
