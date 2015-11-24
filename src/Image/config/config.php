<?php

return [
    "ImageProcessor" => [
        "templates" => [
            "thumb"    => [
                "action"  => "resize",
                "options" => [100, 150],
            ],
            "medium"    => [
                "action"  => "resize",
                "options" => [300, 300],
            ],
            "origin" => [
                "action"  => "origin",
            ]
        ]
    ]
];