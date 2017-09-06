<?php

use Akademiano\Messages\MessagesOpsRoutesStore as MessagesRoutes;

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

    "sendEmails" => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/messages/send",
        ],
        "action" => ["sender", "sendEmails"],
    ],
];
