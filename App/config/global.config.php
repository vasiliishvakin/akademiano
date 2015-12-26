<?php

return [
    "database" => [
        "default" => [
            "name" => "deltaapp",
        ]
    ],
    'view' => [
        'adapter' => 'twig',
        'options' => [
            'cache' => false,
            "debug" => true,
        ],
        "extensions" => [
            "Twig_Extension_Debug",
            "DeltaTwigExt\\AssetExtension",
            "DeltaTwigExt\\UrlExtension",
            "User\\Twig\\UserExtension"
        ],
        "filters" => [
            "cropBySpace" => [["\\DeltaUtils\\StringUtils", "cropBySpace"], ['pre_escape' => 'html']],
            "urlToTag" => [
                ["\\DeltaUtils\\StringUtils", "urlToTag"],
                ['pre_escape' => 'html', 'is_safe' => array('html')]
            ],
            "nl2p" => [["\\DeltaUtils\\StringUtils", "nl2p"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "cutStr" => [["\\DeltaUtils\\StringUtils", "cutStr"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "nl2Array" => [["\\DeltaUtils\\StringUtils", "nl2Array"], []],
            "idStr" => [["\\DeltaUtils\\StringUtils", "toIdStr9"], []],
            "dateIntl" => [["\\DeltaUtils\\Time", "toStrIntl"], []],
            "date2Month" => [["\\DeltaUtils\\Time", "calendarMonth"], ['is_safe' => array('html')]],
        ],
        "urlExtension" => [
            "routeGenerator" => [
                \DeltaCore\Config::DYN_CONF => function ($c) {
                    $router = $c["router"];

                    return [$router, "getUrl"];
                }
            ]
        ],
        "userExtension" => [
            "userManager" => [
                \DeltaCore\Config::DYN_CONF => function ($c) {
                    return $c["userManager"];
                }
            ]
        ]
    ],
    "init" => [
        "setLocale" => function ($c) {
            setlocale(LC_ALL, 'ru_RU.UTF-8');
            setlocale(LC_NUMERIC, "en_US.UTF-8");
            locale_set_default('ru');
        }
    ],
    "Acl" => [
        "adapter" => "\\Acl\\Model\\Adapter\\RegisteredAdapter",
    ],
    "Image" => [
        "watermark" => new \Image\Model\Watermark(["text" => "DeltaApp"]),
    ],
    "modules" => [
        "DeltaSkeletonModule",
        "DeltaDb",
        "Pages",
        "Acl",
        "User",
        "DictDir",
        "SiteMenu",
        "Articles",
        "Attach",
        "Sequence",
        "Image"
    ],
];
