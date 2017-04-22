<?php

return [
    "root" => [
        "patterns" => [
            "type" =>\Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/",
        ],
        "action" => ["index", "index"],
    ],
];
