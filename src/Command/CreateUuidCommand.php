<?php


namespace Akademiano\UUID\Command;


use Akademiano\Operator\Command\Command;
use Akademiano\UUID\UuidComplexShortTables;

class CreateUuidCommand extends Command
{
    const COMMAND_NAME = "create.uuid";

    public function __construct($value = null, $shard = null, $table = null)
    {
        $params["value"] = $value;
        $params["shard"] = $shard;
        $params["table"] = $table;

        parent::__construct($params, UuidComplexShortTables::class);
    }
}
