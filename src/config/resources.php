<?php

return [
    'custodian' => function ($c) {
        $custodian = new \Akademiano\UserEO\Custodian();
        return $custodian;
    },
];
