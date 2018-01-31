<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\EntityInterface;

interface LoaderInterface
{
    public function load(EntityInterface $entity, array $data);

}
