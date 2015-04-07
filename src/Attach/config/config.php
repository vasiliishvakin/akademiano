<?php

use DeltaUtils\FileSystem;

return [
    "Attach" => [
        "sequence" => "attach_files",
        "filesPath" => [
            FileSystem::FST_IMAGE => "data/images",
            "default" => "public/data/files",
        ]
    ],
    "Sequence" => [
        "sequences" => [
            "attach_files",
        ],
    ],
];