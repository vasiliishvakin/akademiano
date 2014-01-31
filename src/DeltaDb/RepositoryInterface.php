<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


use DeltaDb\Adapter\AdapterInterface;

interface RepositoryInterface
{
    public function setAdapter(AdapterInterface $adapter);

    public function create(array $data = null, $entityClass = null);

    public function save(EntityInterface $entity);

    public function delete(EntityInterface $entity);

    public function find(array $criteria = [], $entityClass = null);

    public function findById($id, $entityClass = null);

    public function load(EntityInterface $entity, array $data);

    public function reserve(EntityInterface $entity);

} 