<?php


namespace EntityOperator\Operator;


trait IncludeOperatorTrait
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

}
