<?php


namespace Akademiano\EntityOperator;


use Akademiano\Entity\Entity;

interface GenerateIdInterface
{
    public function genId($class = Entity::class);
}
