<?php

return [
    [
        "methods" => [\DeltaRouter\Route::METHOD_ALL],
        "patterns" => [
            "part" => \DeltaRouter\RoutePattern::PART_PATH,
            "type" => \DeltaRouter\RoutePattern::TYPE_FULL,
            "value" => "/",
        ],
        "action" => ["delta", "index"],
    ]
];