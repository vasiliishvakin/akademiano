<?php

return [
    "main" => [
        ["text" => "Статьи", "route" => "articles_list", "subRoutes" => ["articles_view"]],
    ],
    "admin" => [
        ["text" => "Статьи", "link" => "/admin/articles"],
    ],
];