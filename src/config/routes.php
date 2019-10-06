<?php

use Akademiano\Content\Knowledgebase\Thing\RoutesStore;
use Akademiano\Content\Knowledgebase\Thing\AdminRoutesStore;
use Akademiano\Router\RoutePattern;

return [

    RoutesStore::FILE_VIEW_ROUTE . '_short' => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/things/files/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],

    RoutesStore::FILE_VIEW_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/data/files/things/(?P<template>\w+)/(?P<position>[a-zA-Z0-9/]+)/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],
    'id_file_view' => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/things/files/id(?P<id>\w+)$",
        ],
        "action" => ["files", "id"],
    ],

    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/admin/things/id(?P<id>\w+)/edit",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "/admin/things/(?P<id>\w+)/delete",
        ],
        "action" => ["admin", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "^/things/id(?P<id>\w+)",
        ],
        "action" => ["index", "view"],
    ],

    RoutesStore::TAG_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_REGEXP,
            "value" => "^/things/tag/id(?P<id>\w+)",
        ],
        "action" => ["index", "tag"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/things",
        ],
        "action" => ["index", "list"],
    ],
    AdminRoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/things",
        ],
        "action" => ["admin", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/things/add",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => RoutePattern::TYPE_FULL,
            "value" => "/admin/things/save",
        ],
        "action" => ["admin", "save"],
    ],
];
