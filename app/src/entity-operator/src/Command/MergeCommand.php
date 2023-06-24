<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class MergeCommand extends EntityObjectCommand
{
    /** @var EntityInterface */
    protected $entityMerged;

    /**
     * @return EntityInterface
     */
    public function getEntityMerged(): EntityInterface
    {
        return $this->entityMerged;
    }

    /**
     * @param EntityInterface $entityMerged
     */
    public function setEntityMerged(EntityInterface $entityMerged): MergeCommand
    {
        $this->entityMerged = $entityMerged;
        return $this;
    }
}
