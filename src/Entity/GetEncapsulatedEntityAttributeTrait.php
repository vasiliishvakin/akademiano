<?php


namespace Akademiano\EntityOperator\Entity;


use Akademiano\Delegating\Command\CommandInterface;

trait GetEncapsulatedAttributeTrait
{
    abstract public function delegate(CommandInterface $command,  bool $throwOnEmptyOperator = false);

}
