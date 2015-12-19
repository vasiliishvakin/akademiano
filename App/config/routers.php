<?php

return [
    "root" => [
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/",
        ],
        "action" => ["index", "index"],
    ],

    "admin" => [
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/admin",
        ],
        "action" => ["admin", "index"],

    ]
];
