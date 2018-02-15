<?php

return [
    \Akademiano\Content\Tags\Api\v1\DictionariesApi::API_ID => function (\Akademiano\DI\Container $c) {
        return new \Akademiano\Content\Tags\Api\v1\DictionariesApi($c[Akademiano\Operator\Operator::RESOURCE_ID]);
    },
    \Akademiano\Content\Tags\Api\v1\TagsApi::API_ID => function (\Akademiano\DI\Container $c) {
        $api = new \Akademiano\Content\Tags\Api\v1\TagsApi($c[Akademiano\Operator\Operator::RESOURCE_ID]);
        $api->setTagsDictionariesRelationsApi($c[\Akademiano\Content\Tags\Api\v1\TagsDictionariesRelationsApi::API_ID]);
        return $api;
    },
    \Akademiano\Content\Tags\Api\v1\TagsRelationsApi::API_ID => function (\Akademiano\DI\Container $c) {
        return new \Akademiano\Content\Tags\Api\v1\TagsRelationsApi($c[Akademiano\Operator\Operator::RESOURCE_ID]);
    },
    \Akademiano\Content\Tags\Api\v1\TagsDictionariesRelationsApi::API_ID => function (\Akademiano\DI\Container $c) {
        return new \Akademiano\Content\Tags\Api\v1\TagsDictionariesRelationsApi($c[Akademiano\Operator\Operator::RESOURCE_ID]);
    },
];
