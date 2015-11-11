<?php

return [
    "deltaphp" => [
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/",
        ],
        "action" => ["delta", "index"],
    ],

    "admin" => [
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/admin",
        ],
        "action" => ["admin", "index"],

    ]
];