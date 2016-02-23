<?php

return [
    "login" => ['/login', ['user', 'login']],
    "registration" => ['/registration', ['user', 'registration']],
    "logout" => ['/logout', ['user', 'logout']],
    "user_profile" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^/user/?(?P<id>\w+)?/?(?P<page>\w+)?$",
        ],
        "action" => ["user", "user"],
    ],
    "user_api" => [
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^/api/user/?(?P<action>\w+)?$",
        ],
        "action" => ["api", "index"],
    ],
];
