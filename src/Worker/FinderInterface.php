<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Utils\Object\Collection;
use Akademiano\Entity\EntityInterface;

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
