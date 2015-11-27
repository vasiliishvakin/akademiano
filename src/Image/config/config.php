<?php

return [
    "Image" => [
        "templates" => [
            "thumb"    => [
                "resizeAndCrop" => [150, 150],
                "addWatermark" => new \Image\Model\Watermark(["text"=>"deltaphp/image"]),
                "clear",
                "optimize"
            ],
            "medium"    => [
                "resizeAndCrop" => [300, 300],
                "addWatermark" => new \Image\Model\Watermark(["text"=>"deltaphp/image"]),
                "clear",
                "optimize"
            ],
            "origin" => [
                "addWatermark" => new \Image\Model\Watermark(["text"=>"deltaphp/image"]),
                "clear",
                "optimize" => [95],
            ]
        ]
    ]
];