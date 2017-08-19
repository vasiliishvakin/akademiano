<?php

use Akademiano\Mesages\MessagesOpsRoutesStore as MessagesRoutes;

return [
//    messages admin
    MessagesRoutes::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/messages",
        ],
        "action" => ["adminIndex", "list"],
    ],
    MessagesRoutes::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/messages/add",
        ],
        "action" => ["adminIndex", "form"],
    ],
    MessagesRoutes::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/messages/save",
        ],
        "action" => ["adminIndex", "save"],
    ],

    MessagesRoutes::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/messages/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminIndex", "form"],
    ],
    MessagesRoutes::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/messages/(?P<id>\w+)/delete",
        ],
        "action" => ["adminIndex", "delete"],
    ],
    MessagesRoutes::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/messages/id(?P<id>\w+)",
        ],
        "action" => ["adminIndex", "view"],
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
    MessagesRoutes::LOGIN_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/login",
        ],
        "action" => ["user", "login"],
    ],
    MessagesRoutes::LOGOUT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/logout",
        ],
        "action" => ["user", "logout"],
    ],
];
