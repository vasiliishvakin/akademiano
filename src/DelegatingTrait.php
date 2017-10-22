<?php


namespace Akademiano\Delegating;


use Akademiano\Delegating\Command\CommandInterface;

trait DelegatingTrait
{
    use IncludeOperatorTrait;

    public function delegate(CommandInterface $command)
    {
        $operator = $this->getOperator();
        if (null === $operator) {
            return null;
        }
        return $this->getOperator()->execute($command);
    }
}
