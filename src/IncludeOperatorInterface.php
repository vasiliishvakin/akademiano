<?php


namespace Akademiano\Delegating;


interface IncludeOperatorInterface
{
    public function setOperator(OperatorInterface $operator);

    /**
     * @return OperatorInterface
     */
    public function getOperator():?OperatorInterface;

}
