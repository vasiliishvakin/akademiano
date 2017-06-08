<?php


namespace Akademiano\Operator;


interface IncludeOperatorInterface
{
    public function setOperator(OperatorInterface $operator);

    /**
     * @return OperatorInterface
     */
    public function getOperator();

}
