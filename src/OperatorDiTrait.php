<?php


namespace DeltaPhp\Operator;


trait OperatorDiTrait
{
    abstract public function getDIContainer();

    /**
     * @return \DeltaPhp\Operator\EntityOperator
     */
    public function getOperator()
    {
        return $this->getDIContainer()["Operator"];
    }

}
