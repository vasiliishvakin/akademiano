<?php


namespace Akademiano\EntityOperator;


interface CountableInterface
{
    public function count($class = null, $criteria);

}
