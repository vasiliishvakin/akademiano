<?php

return [

    "login" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/login",
        ],
        "action" => ["user", "login"],
    ],
    "logout" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/logout",
        ],
        "action" => ["user", "logout"],
    ],
];
