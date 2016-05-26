<?php


namespace EntityOperator;


trait EntityOperatorTrait
{
    abstract public function getDIContainer();

    /**
     * @return \EntityOperator\Operator\EntityOperatorInterface
     */
    public function getEntityOperator()
    {
        return $this->getDIContainer()["EntityOperator"];
    }

}
