<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "ArticlesManager" => function ($c) {
            $nm = new \Articles\Model\ArticlesManager();
            $nm->setCategoryManager($c["articleCategoriesManager"]);
            $fm = $c["fileManager"];
            $nm->setFileManager($fm);
            return $nm;
    },
];