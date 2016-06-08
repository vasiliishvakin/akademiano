<?php


namespace DeltaPhp\Operator\Command;


use DeltaPhp\Operator\Entity\EntityInterface;

interface ReserveCommandInterface extends CommandInterface
{
    public function __construct(EntityInterface $entity);
}