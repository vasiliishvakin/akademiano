<?php


namespace DeltaPhp\Operator;


trait OperatorDiTrait
{
    abstract public function getDIContainer();

    /**
     * @return \DeltaPhp\Operator\OperatorInterface
     */
    public function getOperator()
    {
        return $this->getDIContainer()["Operator"];
    }

}
