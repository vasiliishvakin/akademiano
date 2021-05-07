<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\ApiInterface;

interface EntityApiListInterface extends ApiInterface
{
    const DEFAULT_ORDER = "id";

    public function count($criteria = null);

    /**
     * @param null $criteria
     * @param int $page
     * @param int $itemsPerPage
     * @param string $orderBy
     * @return \Akademiano\Api\v1\Items\ItemsPage
     */
    public function find($criteria = null, $page = 1, $orderBy = self::DEFAULT_ORDER, $itemsPerPage = 10);
}
