<?php


namespace DeltaPhp\Operator;


trait IncludeOperatorTrait
{
    /** @var  OperatorInterface */
    protected $operator;

    public function setOperator(OperatorInterface $operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return EntityOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }

}
