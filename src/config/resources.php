<?php

return [
    \Akademiano\Content\Articles\Articles\Api\ArticlesApi::API_ID => function ($c) {
        $operator = $c["operator"];
        $api = new \Akademiano\Content\Articles\Articles\Api\ArticlesApi($operator);
        return $api;
    },
];
