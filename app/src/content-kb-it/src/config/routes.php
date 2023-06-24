<?php

use Akademiano\Content\Knowledgebase\It\RoutesStore;
use Akademiano\Content\Knowledgebase\It\AdminRoutesStore;
use Akademiano\Router\RoutePattern;

return [

    RoutesStore::FILE_VIEW_ROUTE . '_short' => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/it/files/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],

    RoutesStore::FILE_VIEW_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/data/files/it/(?P<template>\w+)/(?P<position>[a-zA-Z0-9/]+)/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],
    'id_file_view' => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/it/files/id(?P<id>\w+)$",
        ],
        "action" => ["files", "id"],
    ],

    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/admin/it/id(?P<id>\w+)/edit",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/admin/it/(?P<id>\w+)/delete",
        ],
        "action" => ["admin", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "^/it/id(?P<id>\w+)",
        ],
        "action" => ["index", "view"],
    ],

    RoutesStore::TAG_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "^/it/tag/id(?P<id>\w+)",
        ],
        "action" => ["index", "tag"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/it",
        ],
        "action" => ["index", "list"],
    ],
    AdminRoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/it",
        ],
        "action" => ["admin", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/it/add",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/it/save",
        ],
        "action" => ["admin", "save"],
    ],
];
