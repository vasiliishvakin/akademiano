<?php

return [
    "admin_users_list" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/users",
        ],
        "action" => ["adminUsers", "list"],
    ],
    "admin_groups_list" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/groups",
        ],
        "action" => ["adminGroups", "list"],
    ],



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
