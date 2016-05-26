<?php


namespace EntityOperator\Command;


interface SubCommandInterface extends CommandInterface
{
    public function getPrefix();
}