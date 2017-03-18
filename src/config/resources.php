<?php

return [
    'custodian' => function ($c) {
        $custodian = new \Akademiano\User\SimpleCustodian();
        return $custodian;
    },
];
