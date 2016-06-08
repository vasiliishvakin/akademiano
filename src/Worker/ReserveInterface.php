<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Entity\EntityInterface;

interface ReserveInterface extends WorkerInterface
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function reserve(EntityInterface $entity);
}
