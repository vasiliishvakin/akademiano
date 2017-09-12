<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\ApiInterface;
use Akademiano\Db\Adapter\AdapterInterface;
use Akademiano\Entity\EntityInterface;

interface EntityApiInterface extends ApiInterface
{

    const DEFAULT_ORDER = "id";

    public function count($criteria);

    public function getDefaultOrder();

    /**
     * @param null $criteria
     * @param int $page
     * @param int $itemsPerPage
     * @param string $orderBy
     * @return \Akademiano\Api\v1\Items\ItemsPage
     */
    public function find($criteria = null, $page = 1, $orderBy = self::DEFAULT_ORDER, $itemsPerPage = 10);

    /**
     * @param $id
     * @return \PhpOption\Option
     */
    public function get($id);

    /**
     * @param array $data
     * @return EntityInterface
     */
    public function save(array $data);

    public function delete($id);
}
