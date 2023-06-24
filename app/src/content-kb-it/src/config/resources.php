<?php

use Akademiano\Content\Knowledgebase\It\Api\v1\ThingsApi;
use Akademiano\Content\Knowledgebase\It\Api\v1\ThingImagesApi;
use Akademiano\Operator\Operator;

return [
    ThingsApi::API_ID => function ($c) {
        $api = new ThingsApi($c[Operator::RESOURCE_ID]);
        $api->setFilesApi($c[ThingImagesApi::API_ID]);
        return $api;
    },
    ThingImagesApi::API_ID => function ($c) {
        return new ThingImagesApi($c[Operator::RESOURCE_ID]);
    },
];
