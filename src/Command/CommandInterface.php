<?php


namespace EntityOperator\Command;


interface CommandInterface
{
    const COMMAND_FIND = "find";
    const COMMAND_GET = "get";
    const COMMAND_SAVE = "save";
    const COMMAND_DELETE = "delete";

    public function getName();

    public function getClass();

    public function getParams($path = null, $default = null);

}
