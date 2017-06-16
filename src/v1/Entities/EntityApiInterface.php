<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\ApiInterface;

interface EntityApiInterface extends ApiInterface
{

    public function count($criteria);

    /**
     * @param null $criteria
     * @param int $page
     * @param int $itemsPerPage
     * @param string $orderBy
     * @return \Akademiano\Api\v1\Items\ItemsPage
     */
    public function find($criteria = null, $page = 1, $orderBy = "id", $itemsPerPage = 10);

    /**
     * @param $id
     * @return \PhpOption\Option
     */
    public function get($id);
}
