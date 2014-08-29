<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "view" => [
        "vars" => [
            "siteMenu" => function($c) {
                return $c["menuManager"];
            }
        ]
    ]
];