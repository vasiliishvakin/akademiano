<?php

use Webmozart\Console\Api\Args\Format\Option;
use \Webmozart\Console\Api\Args\Format\Argument;

return [
    "db" => [
        "description" => "Main db functions",
        "handler" => "\\DeltaDb\\Console\\DbMain",
        "subCommands" => [
            "create" => [
                "handlerMethod" => "handleCreate",
                "description" => "create database",
                "options" => [
                    "owner" => [
                        "shortName" => "o",
                        "description" => "owner of database",
                        "default" => "postgres",
                        "flags" => Option::OPTIONAL_VALUE | Option::STRING,
                        "valueName" => "username"
                    ],
                    "delete" => [
                        "shortName" => "d",
                        "description" => "delete if exist",
                        "flags" => Option::NO_VALUE | Option::BOOLEAN,
                    ],
                    "kill" => [
                        "shortName" => "k",
                        "description" => "kill all connections before delete",
                        "flags" => Option::NO_VALUE | Option::BOOLEAN,
                    ],
                    "ask" => [
                        "shortName" => "a",
                        "description" => "ask all params from console input",
                        "flags" => Option::NO_VALUE | Option::BOOLEAN,
                    ]
                ],
            ],
            "config" => [
                "default" => true,
                "handlerMethod" => "handleConfig",
                "description" => "configure database",
                "options" => [
                    "host" => [
                        "shortName" => "h",
                        "description" => "ip or hostname db server",
                        "default" => "127.0.0.1",
                        "flags" => Option::OPTIONAL_VALUE | Option::STRING,
                        "valueName" => "hostname"
                    ],
                    "user" => [
                        "shortName" => "u",
                        "description" => "database user",
                        "default" => "postgres",
                        "flags" => Option::OPTIONAL_VALUE | Option::STRING,
                        "valueName" => "username"
                    ],
                    "password" => [
                        "shortName" => "W",
                        "description" => "password of db user",
                        "default" => "123",
                        "flags" => Option::OPTIONAL_VALUE | Option::STRING,
                        "valueName" => "password"
                    ],
                    /*  "port" => [
                          "shortName" => "p",
                          "description" => "port for connect",
                          "default" => 5432,
                          "flags" => Option::OPTIONAL_VALUE | Option::INTEGER,
                          "valueName" => "port"
                      ],*/
                    "ask" => [
                        "shortName" => "a",
                        "description" => "ask all params from console input",
                        "flags" => Option::NO_VALUE | Option::BOOLEAN,
                    ]
                ],
                "arguments" => [
                    "database" => [
                        "flags" => Argument::OPTIONAL | Argument::STRING,
                        "default" => "deltapp",
                        "description" => "database name",
                    ],
                ],
            ],
        ],
    ],
];
