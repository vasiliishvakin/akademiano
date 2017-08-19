<?php

return [
    \Akademiano\Messages\Api\v1\MessagesApi::API_ID => function (\Akademiano\DI\Container $c) {
        return new \Akademiano\Messages\Api\v1\MessagesApi($c["operator"]);
    },
];
