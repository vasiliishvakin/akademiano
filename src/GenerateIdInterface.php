<?php


namespace DeltaPhp\Operator;


use DeltaPhp\Operator\Entity\Entity;

interface GenerateIdInterface
{
    public function genId($class = Entity::class);
}
