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
            "250x250" => [
                "resizeAndCrop" => [250, 250],
                "addWatermark" => function (\DeltaCore\Config $c) {
                    return $c->getOrThrow("watermark");
                },
                "clear",
                "optimize"
            ],
            "400x300" => [
                "resizeAndCrop" => [400, 300],
                "addWatermark" => function (\DeltaCore\Config $c) {
                    return $c->getOrThrow("watermark");
                },
                "clear",
                "optimize"
            ],
            "origin" => [
                "addWatermark" => function (\DeltaCore\Config $c) {
                    /** @var \Image\Model\Watermark $watermark */
                    $watermark = clone $c->getOrThrow("watermark");
                    $watermark->setMode(\Image\Model\Watermark::MODE_TEXT_MOSAIC);
                    return $watermark;
                },
                "clear",
                "optimize" => [95],
            ]
        ]
    ]
];
