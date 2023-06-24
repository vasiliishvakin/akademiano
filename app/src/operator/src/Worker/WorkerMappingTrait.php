<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Operator\WorkersMap\RelationInterface;

trait WorkerMappingTrait
{
    public static function getSelfCommandsMapping(array $overwrite = []): array
    {
        $commands = static::getSupportedCommands();
        $relations = [];
        foreach ($commands as $command) {
            $order = $overwrite[$command][RelationInterface::PARAM_ORDER] ?? ($overwrite[RelationInterface::PARAM_ORDER] ?? WorkerSelfMapCommandsInterface::DEFAULT_ORDER);
            $filterFields = static::getMapFieldFilters($command);
            $relation = [
                RelationInterface::PARAM_ORDER => $order,
            ];
            if (!empty($filterFields)) {
                $relation[RelationInterface::PARAM_FILTER_FIELDS] = $filterFields;
            }
            $relations[$command] = $relation;
        }
        return $relations;
    }

    abstract public static function getSupportedCommands(): array;

    abstract public static function getMapFieldFilters(string $command): ?array;
}
