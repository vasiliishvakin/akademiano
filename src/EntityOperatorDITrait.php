<?php


namespace EntityOperator;


trait EntityOperatorDITrait
{
    abstract public function getDIContainer();

    /**
     * @return \EntityOperator\EntityOperatorInterface
     */
    public function getEntityOperator()
    {
        return $this->getDIContainer()["EntityOperator"];
    }

}
