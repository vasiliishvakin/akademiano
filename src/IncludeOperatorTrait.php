<?php


namespace Akademiano\Delegating;


trait IncludeOperatorTrait
{
    /** @var  OperatorInterface */
    protected $operator;

    public function setOperator(OperatorInterface $operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return OperatorInterface
     */
    public function getOperator():?OperatorInterface
    {
        return $this->operator;
    }

}
