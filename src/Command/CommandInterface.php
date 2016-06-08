<?php


namespace DeltaPhp\Operator\Command;


interface CommandInterface
{
    const COMMAND_FIND = "find";
    const COMMAND_COUNT = "count";
    const COMMAND_GET = "get";
    const COMMAND_SAVE = "save";
    const COMMAND_DELETE = "delete";
    const COMMAND_CREATE = "create";
    const COMMAND_LOAD = "load";
    const COMMAND_RESERVE = "reserve";
    const COMMAND_GENERATE_ID = "generate.id";

    public function getName();

    public function getClass();

    public function getParams($path = null, $default = null);

    public function isEmptyClass();

    public function hasClass();

}
