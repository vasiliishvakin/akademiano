<?php

return [
    \Akademiano\Content\Articles\Api\v1\ArticlesApi::API_ID => function ($c) {
        $operator = $c["operator"];
        $api = new \Akademiano\Content\Articles\Api\v1\ArticlesApi($operator);
        $api->setFilesApi($c[\Akademiano\Content\Articles\Api\v1\ArticleFilesApi::API_ID]);
        return $api;
    },
    \Akademiano\Content\Articles\Api\v1\ArticleFilesApi::API_ID => function ($c) {
        return new \Akademiano\Content\Articles\Api\v1\ArticleFilesApi($c["operator"]);
    },
];
