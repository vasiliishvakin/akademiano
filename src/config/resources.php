<?php

return [
    \Akademiano\Content\Articles\Api\v1\ArticlesApi::API_ID => function ($c) {
        $api = new \Akademiano\Content\Articles\Api\v1\ArticlesApi($c[\Akademiano\Operator\Operator::RESOURCE_ID]);
        $api->setFilesApi($c[\Akademiano\Content\Articles\Api\v1\ArticleImagesApi::API_ID]);
        $api->setTagsArticlesRelationsApi($c[\Akademiano\Content\Articles\Api\v1\TagsArticlesRelationsApi::API_ID]);
        return $api;
    },
    \Akademiano\Content\Articles\Api\v1\ArticleImagesApi::API_ID => function ($c) {
        return new \Akademiano\Content\Articles\Api\v1\ArticleImagesApi($c[\Akademiano\Operator\Operator::RESOURCE_ID]);
    },
    \Akademiano\Content\Articles\Api\v1\ArticleTagsApi::API_ID => function ($c) {
        return new \Akademiano\Content\Articles\Api\v1\ArticleTagsApi($c[\Akademiano\Operator\Operator::RESOURCE_ID]);
    },
    \Akademiano\Content\Articles\Api\v1\TagsArticlesRelationsApi::API_ID => function ($c) {
        return new \Akademiano\Content\Articles\Api\v1\TagsArticlesRelationsApi($c[\Akademiano\Operator\Operator::RESOURCE_ID]);
    },
];
