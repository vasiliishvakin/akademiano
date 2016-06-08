<?php


namespace DeltaPhp\Operator;


use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Entity\EntityInterface;

interface FinderInterface extends CountableInterface
{
    /**
     * @param null $class
     * @param $criteria
     * @param null $limit
     * @param null $offset
     * @param null $orderBy
     * @return Collection|EntityInterface[]
     */
    public function find($class = null, $criteria, $limit = null, $offset = null, $orderBy = null);

    /**
     * @param null $class
     * @param $id
     * @return EntityInterface
     */
    public function get($class = null, $id);

}
