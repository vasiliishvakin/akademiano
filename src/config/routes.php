<?php

use Akademiano\HeraldMessages\AdminRoutes;
use Akademiano\HeraldMessages\Routes;

return [

    //send
    Routes::SEND_ROUTE => [
        "methods" => [
            \Akademiano\Router\Route::METHOD_POST,
            \Akademiano\Router\Route::METHOD_PUT,
            \Akademiano\Router\Route::METHOD_GET,
        ],
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/herald-messages/send/(?P<id>\w+)",
        ],
        "action" => ["sender", "send"],
    ],

    //user routes
    Routes::ADD_ROUTE => [
        "methods" => \Akademiano\Router\Route::METHOD_GET,
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/herald-messages/add",
        ],
        "action" => ["messages", "form"],
    ],
    Routes::EDIT_ROUTE => [
        "methods" => \Akademiano\Router\Route::METHOD_GET,
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/herald-messages/edit/(?P<id>\w+)",
        ],
        "action" => ["messages", "form"],
    ],

    Routes::LIST_ROUTE => [
        "methods" => \Akademiano\Router\Route::METHOD_GET,
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/herald-messages",
        ],
        "action" => ["messages", "list"],
    ],
    Routes::SAVE_ROUTE => [
        "methods" => \Akademiano\Router\Route::METHOD_POST,
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/herald-messages",
        ],
        "action" => ["messages", "save"],
    ],
    Routes::CHANGE_ROUTE => [
        "methods" => [
            \Akademiano\Router\Route::METHOD_POST,
            \Akademiano\Router\Route::METHOD_PUT,
            \Akademiano\Router\Route::METHOD_DELETE,
        ],
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/herald-messages/(?P<id>\w+)",
        ],
        "action" => ["messages", "change"],
    ],
    Routes::VIEW_ROUTE => [
        "methods" => \Akademiano\Router\Route::METHOD_GET,
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/herald-messages/(?P<id>\w+)",
        ],
        "action" => ["messages", "view"],
    ],


//    messages admin
    AdminRoutes::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/herald-messages",
        ],
        "action" => ["adminIndex", "list"],
    ],
    AdminRoutes::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/herald-messages/add",
        ],
        "action" => ["adminIndex", "form"],
    ],
    AdminRoutes::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/herald-messages/save",
        ],
        "action" => ["adminIndex", "save"],
    ],

    AdminRoutes::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/herald-messages/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminIndex", "form"],
    ],
    AdminRoutes::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/herald-messages/(?P<id>\w+)/delete",
        ],
        "action" => ["adminIndex", "delete"],
    ],
    AdminRoutes::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/herald-messages/id(?P<id>\w+)",
        ],
        "action" => ["adminIndex", "view"],
    ],
];
