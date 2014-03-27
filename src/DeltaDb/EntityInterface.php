<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb;


interface EntityInterface
{
    public function getId();

    /**
     * @return array
     */
    public function getFieldsList();

} 