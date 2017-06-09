<?php


namespace Akademiano\Operator\Command;


interface CommandInterface
{
    const COMMAND_UNDEFINED = "undefined";

    public function getName();

    public function getClass();

    public function getParams($path = null, $default = null);

    public function setParams($params, $path = null);

    public function addParams($params, $path = null);

    public function hasParam($path);

    public function isEmptyClass();

    public function hasClass();

}
