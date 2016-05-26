<?php


namespace EntityOperator;


use EntityOperator\Operator\EntityOperatorInterface;

trait EntityOperatorTrait
{
    abstract public function getDIContainer();

    /**
     * @return EntityOperatorInterface
     */
    public function getEntityOperator()
    {
        return $this->getDIContainer()["EntityOperator"];
    }

}