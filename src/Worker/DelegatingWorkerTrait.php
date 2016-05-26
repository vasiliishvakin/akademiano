<?php


namespace EntityOperator\Worker;


use EntityOperator\Command\CommandInterface;
use EntityOperator\Operator\OperatorInterface;

trait DelegatingWorkerTrait
{
    /** @var  OperatorInterface */
    protected $operator;

    public function setOperator(OperatorInterface $operator)
    {
        $this->operator = $operator;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function delegate(CommandInterface $command)
    {
        return $this->getOperator()->execute($command);
    }


}
