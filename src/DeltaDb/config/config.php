<?php
return [
    "init" => [
        "dba" => function($c) {
            \DeltaDb\DbaStorage::setDefault($c["dbDefaultAdapterClosure"]);
        },
    ]
];
