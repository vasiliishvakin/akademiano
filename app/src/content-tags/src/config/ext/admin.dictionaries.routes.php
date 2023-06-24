<?php

use Akademiano\Content\Tags\AdminDictionariesRoutesStore as RoutesStore;

return [
    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/dictionaries/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminDictionaries", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/dictionaries/(?P<id>\w+)/delete",
        ],
        "action" => ["adminDictionaries", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/dictionaries/id(?P<id>\w+)",
        ],
        "action" => ["adminDictionaries", "view"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/dictionaries",
        ],
        "action" => ["adminDictionaries", "list"],
    ],
    \Akademiano\Content\Articles\AdminRoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/dictionaries",
        ],
        "action" => ["adminDictionaries", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/dictionaries/add",
        ],
        "action" => ["adminDictionaries", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/dictionaries/save",
        ],
        "action" => ["adminDictionaries", "save"],
    ],
];

