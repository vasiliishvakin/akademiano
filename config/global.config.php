<?php

return [
    'view' => [
        'adapter' => 'twig',
        'templateDirs' => [
            'templates',
        ],
        'options' => [
            'cache' => 'data/cache',
            'auto_reload' => true,
        ],
    ]

];