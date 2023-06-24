<?php


namespace Akademiano\Delegating;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Delegating\Exception\EmptyOperatorException;

trait DelegatingTrait
{
    use IncludeOperatorTrait;

    /**
     * @param CommandInterface $command
     * @param bool $throwOnEmptyOperator
     * @return mixed|\Akademiano\Utils\Object\Collection|null
     * @throws EmptyOperatorException;
     */
    public function delegate(CommandInterface $command, bool $throwOnEmptyOperator = false)
    {
        $operator = $this->getOperator();
        if (null === $operator) {
            if ($throwOnEmptyOperator) {
                throw new EmptyOperatorException();
            } else {
                return null;
            }
        }
        return $operator->execute($command);
    }
}
