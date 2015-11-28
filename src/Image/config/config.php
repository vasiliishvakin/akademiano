<?php

return [
    "Image" => [
        "watermark" => new \Image\Model\Watermark(["text" => "deltaphp/image"]),
        "templates" => [
            "thumb" => [
                "resizeAndCrop" => [150, 150],
                "addWatermark" => function (\DeltaCore\Config $c) {
                    $watermark = clone  $c->getOrThrow("watermark");
                    $watermark->setSize(10);

                    return $watermark;
                },
                "clear",
                "optimize"
            ],
            "medium" => [
                "resizeAndCrop" => [300, 300],
                "addWatermark" => function (\DeltaCore\Config $c) {
                    return $c->getOrThrow("watermark");
                },
                "clear",
                "optimize"
            ],
            "origin" => [
                "addWatermark" => function (\DeltaCore\Config $c) {
                    return $c->getOrThrow("watermark");
                },
                "clear",
                "optimize" => [95],
            ]
        ]
    ]
];
