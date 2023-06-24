<?php

use \Akademiano\EntityOperator\Worker\TablesIdsWorker;
use Akademiano\Config\Permanent\PermanentStorageFile;
use Akademiano\Config\FS\ConfigFile;

return [
    'entityOperator' => [
        'workers' => [
            TablesIdsWorker::WORKER_ID => [
                'configStorage' => [
                    PermanentStorageFile::class => [
                        'file' => ROOT_DIR . '/src/config/' . ConfigFile::TYPE_AUTO . '.config.' . ConfigFile::EXT
                    ],
                ],
            ],
        ],
    ],
];
