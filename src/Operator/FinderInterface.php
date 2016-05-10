<?php


namespace EntityOperator\Operator;


interface FinderInterface
{
    public function find($criteria, $limit = null, $offset = null);

    public function get($id);

}