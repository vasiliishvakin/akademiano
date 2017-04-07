<?php

return [
    "root" => [
        "patterns" => [
            "type" =>\Akademiano\Router\RoutePattern::TYPE_FIRST_PREFIX,
            "value" => "/",
        ],
        "action" => ["index", "index"],
    ],
];
