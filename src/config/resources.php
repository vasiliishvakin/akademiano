<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "EntityOperator" => function ($c) {
        $e = new \EntityOperator\EntityOperator();
        $e->setDependencies($c);
        return $e;
    },
];
