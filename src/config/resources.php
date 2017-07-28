<?php

return [
    "operator" => function ($c) {
        $e = new \Akademiano\Operator\Operator();
        $e->setDependencies($c);
        return $e;
    },
];
