<?php

return [
    'view' => [
        'adapter' => 'twig',
        'templateDirs' => [
            'public/templates',
        ],
        'options' => [
            'cache' => 'data/cache',
            'auto_reload' => true,
        ],
    ]

];