<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Entity\EntityInterface;

interface EntityObjectCommandInterface
{
    public function getEntity():EntityInterface;
}
