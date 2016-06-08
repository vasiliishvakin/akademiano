<?php


namespace DeltaPhp\Operator\Command;


interface SubCommandInterface extends CommandInterface
{
    public function getPrefix();
}
