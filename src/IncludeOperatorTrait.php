<?php


namespace Akademiano\Operator;


trait IncludeOperatorTrait
{
    /** @var  OperatorInterface */
    protected $operator;

    public function setOperator(OperatorInterface $operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

}
