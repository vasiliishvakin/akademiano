<?php


namespace Akademiano\Operator\Command;


interface SubCommandInterface extends CommandInterface
{
    public function getPrefix();
}
