<?php

return [
    "Operator" => function ($c) {
        $e = new \Akademiano\EntityOperator\EntityOperator();
        $e->setDependencies($c);
        return $e;
    },
];
