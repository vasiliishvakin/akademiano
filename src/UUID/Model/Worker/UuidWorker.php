<?php


namespace UUID\Model\Worker;

use EntityOperator\Command\CommandInterface;
use EntityOperator\Worker\Exception\NotSupportedCommand;
use EntityOperator\Worker\WorkerInterface;
use UUID\Model\Command\CreateUuidCommand;
use UUID\Model\Parts\UuidFactoryTrait;
use UUID\Model\UuidComplexShortTables;

class UuidWorker implements WorkerInterface
{
    use UuidFactoryTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case CommandInterface::COMMAND_CREATE:
            case CreateUuidCommand::COMMAND_UUID_CREATE :
                $value = $command->getParams("value");
                $class = $command->getClass() ?: UuidComplexShortTables::class;
                $epoch = $command->getParams("epoch");
                return $this->toUiid($value, $class, $epoch);
            
            default:
                throw new NotSupportedCommand($command);
        }
    }

    public function toUiid($value, $class = UuidComplexShortTables::class, $epoch = null)
    {
        return $this->getUuidFactory()->create($value, $epoch, $class);
    }
}
