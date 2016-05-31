<?php


namespace EntityOperator\Operator;


interface IncludeOperatorInterface
{
    public function setOperator(OperatorInterface $operator);

    /**
     * @return OperatorInterface
     */
    public function getOperator();

}