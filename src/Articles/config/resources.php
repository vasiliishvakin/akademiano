<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "NewsManager" => function ($c) {
            $nm = new \Articles\Model\ArticlesManager();
            $df = $c["directoryFactory"];
            $cm = $df->getManager("article_categories");
            $nm->setCategoryManager($cm);
            $fm = $c["fileManager"];
            $nm->setFileManager($fm);
            return $nm;
    },
];