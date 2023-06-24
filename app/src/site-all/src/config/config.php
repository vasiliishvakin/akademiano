<?php
return [
    'view' => [
        'adapter' => 'twig',
        'options' => [
            'cache' => true,
            "debug" => false,
        ],
        "extensions" => [
            "Twig_Extension_Debug",
            "Twig_Extension_StringLoader",
            "Akademiano\\Twig\\Extensions\\AssetExtension",
            "Akademiano\\Twig\\Extensions\\UrlExtension",
            "Akademiano\\User\\Twig\\UserExtension",
        ],
        "filters" => [
            "cropBySpace" => [["\\Aademiano\\Utils\\StringUtils", "cropBySpace"], ['pre_escape' => 'html']],
            "urlToTag" => [
                ["\\Aademiano\\Utils\\StringUtils", "urlToTag"],
                ['pre_escape' => 'html', 'is_safe' => array('html')]
            ],
            "nl2p" => [["\\Aademiano\\Utils\\StringUtils", "nl2p"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "cutStr" => [["\\Aademiano\\Utils\\StringUtils", "cutStr"], ['pre_escape' => 'html', 'is_safe' => array('html')]],
            "nl2Array" => [["\\Aademiano\\Utils\\StringUtils", "nl2Array"], []],
            "dechex" => [function($id) {
                if($id instanceof \Akademiano\Entity\UuidInterface) {
                    $id = (string) $id->getHex();
                    return$id;
                } elseif (is_numeric($id)) {
                    return dechex((int)$id);
                } else {
                    throw new RuntimeException("Culd not convert to hex not numeric or uuid item");
                }
            }, []],
            "dateIntl" => [["\\Aademiano\\Utils\\Time", "toStrIntl"], []],
            "date2Month" => [["\\Aademiano\\Utils\\Time", "calendarMonth"], ['is_safe' => array('html')]],
        ],
        "urlExtension" => [
            "routeGenerator" => [
                \Akademiano\Config\Config::DYN_CONF => function ($c) {
                    $router = $c["router"];
                    return [$router, "getUrl"];
                }
            ]
        ],
        "userExtension" => [
            "custodian" => [
                \Akademiano\Config\Config::DYN_CONF => function ($c) {
                    return $c["custodian"];
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
        "adapter" => "Akademiano\\Acl\\Adapter\\RegisteredAdapter",
    ],
    "modules" => [
        "Akademiano\\User"
    ],
];
