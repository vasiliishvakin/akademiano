<?php

use \Akademiano\User\SimpleCustodian;

return [
    SimpleCustodian::RESOURCE_ID => function ($c) {
        $custodian = new SimpleCustodian();
        return $custodian;
    },
];
