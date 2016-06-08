<?php


namespace DeltaPhp\Operator;


use DeltaPhp\Operator\Command\CommandInterface;

trait DelegatingTrait
{
    use IncludeOperatorTrait;

    public function delegate(CommandInterface $command)
    {
        return $this->getOperator()->execute($command);
    }
}
