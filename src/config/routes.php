<?php

use Akademiano\Content\Countries\CountriesOpsRoutesStore as RoutesStore;

return [

    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/countries/id(?P<id>\w+)/edit",
        ],
        "action" => ["index", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/countries/(?P<id>\w+)/delete",
        ],
        "action" => ["index", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/countries/id(?P<id>\w+)",
        ],
        "action" => ["index", "view"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries",
        ],
        "action" => ["index", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries/add",
        ],
        "action" => ["index", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries/save",
        ],
        "action" => ["index", "save"],
    ],
];
