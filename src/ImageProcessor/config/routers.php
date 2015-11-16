<?php

return [
    "image_view" => [
        "methods" => [\DeltaRouter\Route::METHOD_GET],
        "patterns" => [
            "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
            "value" => "^(?<directory>/data/images/[a-z0-9/]+)/(?P<template>\w+)/(?P<file>\w+\.\w+)$",
        ],
        "action" => ["index", "index"]
    ],
];
