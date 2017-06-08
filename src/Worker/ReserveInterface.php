<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\EntityInterface;
use Akademiano\Operator\Worker\WorkerInterface;

interface ReserveInterface extends WorkerInterface
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function reserve(EntityInterface $entity);
}
