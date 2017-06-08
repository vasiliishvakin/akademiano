<?php


namespace Akademiano\EntityOperator;


use Akademiano\Entity\EntityInterface;

interface LoaderInterface
{
    public function load(EntityInterface $entity, array $data);

}
