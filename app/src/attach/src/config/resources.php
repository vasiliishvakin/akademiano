<?php

return[];

return [
    \Akademiano\Attach\Api\v1\LinkedFilesApi::API_ID => function (\Pimple\Container $c) {
        return new \Akademiano\Attach\Api\v1\LinkedFilesApi($c["operator"]);
    },
    \Akademiano\Attach\Api\v1\EntityFileRelationsApi::API_ID => function (\Pimple\Container $c) {
        return new \Akademiano\Attach\Api\v1\EntityFileRelationsApi($c["operator"]);
    },
    \Akademiano\Attach\Api\v1\RelatedFilesApi::API_ID => function (\Pimple\Container $c) {
        $api = new \Akademiano\Attach\Api\v1\RelatedFilesApi($c["operator"]);
        $api->setRelationsApi($c[\Akademiano\Attach\Api\v1\EntityFileRelationsApi::API_ID]);
        return $api;
    },
];
