<?php

return [
    "git" => [
        "description" => "Main application functions",
        "handler" => function () {
            return new \Akademiano\App\Console\Git();
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
