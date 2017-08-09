<?php

use Akademiano\Content\Countries\CountriesOpsRoutesStore as RoutesStore;

return [

    RoutesStore::EDIT_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/countries/id(?P<id>\w+)/edit",
        ],
        "action" => ["adminCountries", "form"],
    ],
    RoutesStore::DELETE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/countries/(?P<id>\w+)/delete",
        ],
        "action" => ["adminCountries", "delete"],
    ],
    RoutesStore::VIEW_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/countries/id(?P<id>\w+)",
        ],
        "action" => ["adminCountries", "view"],
    ],

    RoutesStore::LIST_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries",
        ],
        "action" => ["adminCountries", "list"],
    ],
    RoutesStore::ADD_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries/add",
        ],
        "action" => ["adminCountries", "form"],
    ],
    RoutesStore::SAVE_ROUTE => [
        "patterns" => [
            "type" => \Akademiano\Router\RoutePattern::TYPE_FULL,
            "value" => "/admin/countries/save",
        ],
        "action" => ["adminCountries", "save"],
    ],
];
