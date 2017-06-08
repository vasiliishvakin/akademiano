<?php


namespace Akademiano\Operator;


trait OperatorDiTrait
{
    abstract public function getDIContainer();

    /**
     * @return Operator
     */
    public function getOperator()
    {
        return $this->getDIContainer()["Operator"];
    }

}
