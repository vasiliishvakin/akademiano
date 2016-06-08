<?php


namespace DeltaPhp\Operator;


use DeltaPhp\Operator\Entity\EntityInterface;

interface LoaderInterface
{
    public function load(EntityInterface $entity, array $data);

}
