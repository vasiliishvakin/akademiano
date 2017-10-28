<?php


namespace Akademiano\Delegating\Command;


interface CommandInterface
{
    const COMMAND_UNDEFINED = "undefined";

    public function getName();

    public function getClass();

    public function setClass($class);

    public function getParams($path = null, $default = null);

    public function setParams(array $params, $path = null);

    public function addParams($params, $path = null);

    public function hasParam($path);

    public function isEmptyClass();

    public function hasClass();
}
