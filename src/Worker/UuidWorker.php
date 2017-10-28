<?php


namespace Akademiano\UUID\Worker;

use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\UUID\Command\CreateUuidCommand;
use Akademiano\UUID\Parts\UuidFactoryTrait;
use Akademiano\UUID\UuidComplexShortTables;

class UuidWorker implements WorkerInterface
{
    use UuidFactoryTrait;
    use WorkerMetaMapPropertiesTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case CreateCommand::COMMAND_NAME:
            case CreateUuidCommand::COMMAND_NAME :
                $value = $command->getParams("value");
                $class = $command->getClass() ?: UuidComplexShortTables::class;
                $epoch = $command->getParams("epoch");
                return $this->toUuid($value, $class, $epoch);
            
            default:
                throw new NotSupportedCommandException($command);
        }
    }

    public function toUuid($value, $class = UuidComplexShortTables::class, $epoch = null)
    {
        return $this->getUuidFactory()->create($value, $epoch, $class);
    }
}
