<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\EntityOperator\WorkersMap\Filter\RelationCommandEntityClassValueExtractor;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\Entity;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Carbon\Carbon;


class EntityCreatorWorker implements EntityWorkerInterface, DelegatingInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    public const WORKER_ID = 'entityCreatorWorker';

    use DelegatingTrait;
    use WorkerSelfInstanceTrait;
    use WorkerMappingTrait {
        getMapFieldFilters as private wmpGetFieldFilters;
    }

    public static function getSupportedCommands(): array
    {
        return [
            CreateCommand::class,
        ];
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch ($command) {
            case CreateCommand::class:
                return [
                    EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS => [
                        FilterFieldInterface::PARAM_ASSERTION => Entity::class,
                        FilterFieldInterface::PARAM_EXTRACTOR => RelationCommandEntityClassValueExtractor::class,
                    ]
                ];
            default:
                return self::wmpGetFieldFilters($command);
        }
    }


    public function create(string  $class)
    {
        if (null === $class) {
            $class = Entity::class;
        }

        if ($class[0] !== "\\") {
            $class = "\\" . $class;
        }
        $entity = new $class();
        if ($entity instanceof DelegatingInterface) {
            $entity->setOperator($this->getOperator());
        }
        if ($entity instanceof EntityInterface) {
            $entity->setCreated(new Carbon());
        }
        return $entity;
    }

    public function execute(CommandInterface $command)
    {
        switch (true) {
            case $command instanceof CreateCommand:
                return $this->create($command->getEntityClass());
            default:
                throw new NotSupportedCommandException($command);
        }
    }
}
