<?php

return [
    \Akademiano\Content\Countries\Api\v1\CountriesApi::API_ID => function ($c) {
        return new \Akademiano\Content\Countries\Api\v1\CountriesApi( $c["operator"]);
    },
];
