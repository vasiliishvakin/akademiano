<?php

return [
    "Operator" => function ($c) {
        $e = new \DeltaPhp\Operator\EntityOperator();
        $e->setDependencies($c);
        return $e;
    },
];
