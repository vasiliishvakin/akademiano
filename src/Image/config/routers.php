<?php

return [
    "image_view" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^/(?<parentDir>data/images)/(?P<template>\w+)/(?P<subDir>[a-z0-9/]+)/(?P<file>\w+\.\w+)$",
        ],
        "action" => ["index", "index"]
    ],
];
