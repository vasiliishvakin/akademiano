<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\ApiInterface;
use Akademiano\Db\Adapter\AdapterInterface;
use Akademiano\Entity\EntityInterface;

interface EntityApiInterface extends EntityApiListInterface, EntityApiMetadataInterface
{

    public function getDefaultOrder();

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
