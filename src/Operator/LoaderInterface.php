<?php


namespace EntityOperator\Operator;


use EntityOperator\Entity\EntityInterface;

interface LoaderInterface
{
    public function load(EntityInterface $entity, array $data);

}
