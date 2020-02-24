<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Operator\Command\WorkerInfoCommand;

class KeeperWorkerInfoCommand extends WorkerInfoCommand
{
//    const COMMAND_NAME = WorkerInfoCommand::COMMAND_NAME . '.keeper_worker';

    const ATTRIBUTE_TABLE_ID=1;
    const ATTRIBUTE_TABLE_NAME = 3;
    const ATTRIBUTE_FIELDS = 7;

    public function __construct($workerId, $attribute = self::ATTRIBUTE_TABLE_ID)
    {
        parent::__construct($workerId, $attribute);
    }
}
