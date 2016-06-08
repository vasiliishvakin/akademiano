<?php


namespace DeltaPhp\Operator\Worker;


use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Entity\EntityInterface;

interface FinderInterface extends CountableInterface
{
    /**
     * @param $criteria
     * @param null $limit
     * @param null $offset
     * @param null $orderBy
     * @return Collection
     */
    public function find($criteria, $limit = null, $offset = null, $orderBy = null);

    /**
     * @param $id
     * @return EntityInterface
     */
    public function get($id);

}
