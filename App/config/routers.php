<?php

return [
    "root" => [
        "patterns" => [
            "type" =>\Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/",
        ],
        "action" => ["index", "index"],
    ],

    "admin" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin",
        ],
        "action" => ["admin", "index"],

    ]
];
