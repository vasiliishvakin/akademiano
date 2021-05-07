<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\EntityInterface;

interface GetterInterface
{
    /**
     * @param $id
     * @return EntityInterface
     */
    public function get($id);
}
