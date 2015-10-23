<?php

return [
    'view' => [
        'adapter' => 'twig',
        'templateDirs' => [
            'templates',
        ],
        'options' => [
            //'cache' => 'data/cache',
            //'auto_reload' => true,
            'cache' => false,
            "debug" => true,
        ],
        "extensions" => [
            "Twig_Extension_Debug",
        ],
    ],
    "modules" => [
        "DeltaSkeletonModule",
    ]

];