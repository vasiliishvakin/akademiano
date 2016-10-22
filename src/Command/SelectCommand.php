<?php


namespace DeltaPhp\Operator\Command;

use DeltaDb\D2QL\Select;

class SelectCommand extends Command implements CommandInterface
{
    const COMMAND_SELECT = "select";

    protected $name = self::COMMAND_SELECT;


    public function __construct($class, Select $select, array $params = [])
    {
        $this->setClass($class);
        $params["select"] = $select;
        $this->params = $params;
    }
}
