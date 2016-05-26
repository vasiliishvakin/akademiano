<?php


namespace EntityOperator\Operator;


interface CountableInterface
{
    public function count($class = null, $criteria);

}