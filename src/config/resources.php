<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "Operator" => function ($c) {
        $e = new \DeltaPhp\Operator\Operator();
        $e->setDependencies($c);
        return $e;
    },
];
