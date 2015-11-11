<?php
return [
//    "main" => [
//        ["text" => "Места", "link" => "/places", "order" => 10 ],
//        ["text" => "Организации", "link" => "/organizations", "order" => 11],
//        ["text" => "Статьи", "link" => "/articles", "order" => 20],
//        ["text" => "Фотографии", "link" => "/places/photos", "order" => 30],
//    ],

    "admin" => [
        ["text" => "Сайт", "link" => "/", "order" => -10],
        ["text" => "Админка", "route" => "admin"],
        ["text" => "Словари", "route" => "dictdir_list", "order" => 20],
        ["text" => "Страницы", "route" => "pages_list", "order" => 20],
        ["text" => "Профиль", "route" => "user",  "order" => 30],
    ]
];