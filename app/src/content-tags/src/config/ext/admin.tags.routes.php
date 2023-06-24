<?php

use Akademiano\Content\Tags\AdminTagsRoutesStore as RoutesStore;

return [
    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/tags/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminTags", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/tags/(?P<id>\w+)/delete",
        ],
        "action" => ["adminTags", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/tags/id(?P<id>\w+)",
        ],
        "action" => ["adminTags", "view"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/tags",
        ],
        "action" => ["adminTags", "list"],
    ],
    \Akademiano\Content\Articles\AdminRoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/tags",
        ],
        "action" => ["adminTags", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/tags/add",
        ],
        "action" => ["adminTags", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/tags/save",
        ],
        "action" => ["adminTags", "save"],
    ],
];

