<?php

return [
    "articles_view" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^/articles/id(?P<id>\w+)",
        ],
        "action" => ["index", "view"]
    ],

    "articles_list" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FIRST_PREFIX,
            "value" => "^/articles/?(?P<section>\w+)?/?(?P<id>\d+)?$",
        ],
        "action" => ["index", "list"]
    ],

    "sitemap_articles" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FIRST_PREFIX,
            "value" => "/articles-list",
        ],
        "action" => ["list", "list"]
    ],

    "api_articles_dates" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FIRST_PREFIX,
            "value" => "/api/article/dates",
        ],
        "action" => ["api", "dates"]
    ],

    "articles_admin_add" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/admin/articles/add",
        ],
        "action" => ["admin", "form"]
    ],

    "articles_admin_edit" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/articles/edit/(?P<id>\d+)",
        ],
        "action" => ["admin", "form"]
    ],
    "articles_admin_save" => [
        "methods" => [\DeltaRouter\Route::METHOD_POST],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/admin/articles/save",
        ],
        "action" => ["admin", "save"]
    ],

    "articles_admin_rm" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "/admin/articles/rm/(?P<id>\d+)",
        ],
        "action" => ["admin", "rm"]
    ],

    "articles_admin_list" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^/admin/articles(/(?P<section>\w+)/(?P<id>\d+))?",
        ],
        "action" => ["admin", "list"]
    ],

];
