<?php


namespace EntityOperator\Operator;


use EntityOperator\Command\CommandInterface;

trait DelegatingTrait
{
    use IncludeOperatorTrait;

    public function delegate(CommandInterface $command)
    {
        return $this->getOperator()->execute($command);
    }
}
