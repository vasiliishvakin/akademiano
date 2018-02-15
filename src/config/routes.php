<?php

use Akademiano\Content\Articles\RoutesStore;

return [

    RoutesStore::FILE_VIEW_ROUTE . '_short' => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/articles/files/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],

    RoutesStore::FILE_VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/data/files/articles/(?P<template>\w+)/(?P<position>[a-zA-Z0-9/]+)/id(?P<id>\w+)\.(?P<extension>\w+)$",
        ],
        "action" => ["files", "name"],
    ],
    'id_file_view' => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/articles/files/id(?P<id>\w+)$",
        ],
        "action" => ["files", "id"],
    ],

    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/articles/id(?P<id>\w+)/edit",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/articles/(?P<id>\w+)/delete",
        ],
        "action" => ["admin", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/articles/id(?P<id>\w+)",
        ],
        "action" => ["index", "view"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/articles",
        ],
        "action" => ["index", "list"],
    ],
    \Akademiano\Content\Articles\AdminRoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/articles",
        ],
        "action" => ["admin", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/articles/add",
        ],
        "action" => ["admin", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/articles/save",
        ],
        "action" => ["admin", "save"],
    ],
];
