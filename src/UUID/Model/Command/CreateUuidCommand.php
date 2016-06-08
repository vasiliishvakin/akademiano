<?php


namespace UUID\Model\Command;


use EntityOperator\Command\Command;
use EntityOperator\Command\CommandInterface;
use UUID\Model\UuidComplexShort;
use UUID\Model\UuidComplexShortTables;

class CreateUuidCommand extends Command implements CommandInterface
{
    const COMMAND_UUID_CREATE = "create.uuid";

    public function __construct($value = null, $shard = null, $table = null)
    {
        $params["value"] = $value;
        $params["shard"] = $shard;
        $params["table"] = $table;

        parent::__construct($params, UuidComplexShortTables::class, self::COMMAND_UUID_CREATE);
    }
}
