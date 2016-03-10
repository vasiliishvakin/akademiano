<?php

use Webmozart\Console\Api\Args\Format\Option;
use \Webmozart\Console\Api\Args\Format\Argument;

return [
    "git" => [
        "description" => "Main application functions",
        "handler" => function () {
            return new \App\Console\Git();
        },
        "subCommands" => [
            "branch" => [
                "handlerMethod" => "handleBranch",
                "description" => "get project branch",
            ],
            "version" => [
                "default" => true,
                "handlerMethod" => "handleVersion",
                "description" => "get project version",
            ],
        ],
    ],
];
