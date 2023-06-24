<?php

use Akademiano\UserEO\UsersOpsRoutesStore as UsersRoutes;
use Akademiano\UserEO\GroupsOpsRoutesStore as GroupsRoutes;

return [
//    users admin
    UsersRoutes::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/users",
        ],
        "action" => ["adminUsers", "list"],
    ],

    UsersRoutes::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/users/add",
        ],
        "action" => ["adminUsers", "form"],
    ],
    UsersRoutes::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/users/save",
        ],
        "action" => ["adminUsers", "save"],
    ],

    UsersRoutes::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/users/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminUsers", "form"],
    ],
    UsersRoutes::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/users/(?P<id>\w+)/delete",
        ],
        "action" => ["adminUsers", "delete"],
    ],
    UsersRoutes::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/users/id(?P<id>\w+)",
        ],
        "action" => ["adminUsers", "view"],
    ],


//    groups admin
    GroupsRoutes::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/groups",
        ],
        "action" => ["adminGroups", "list"],
    ],

    GroupsRoutes::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/groups/add",
        ],
        "action" => ["adminGroups", "form"],
    ],
    GroupsRoutes::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/groups/save",
        ],
        "action" => ["adminGroups", "save"],
    ],

    GroupsRoutes::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/groups/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminGroups", "form"],
    ],
    GroupsRoutes::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/groups/(?P<id>\w+)/delete",
        ],
        "action" => ["adminGroups", "delete"],
    ],
    GroupsRoutes::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/groups/id(?P<id>\w+)",
        ],
        "action" => ["adminGroups", "view"],
    ],


//user shared
    UsersRoutes::LOGIN_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/login",
        ],
        "action" => ["user", "login"],
    ],
    UsersRoutes::LOGOUT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/logout",
        ],
        "action" => ["user", "logout"],
    ],
    UsersRoutes::PROFILE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/user",
        ],
        "action" => ["user", "profile"],
    ],
];
