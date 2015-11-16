<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "articleCategoriesManager" => function ($c) {
        /** @var \DictDir\Model\DirectoryFactory $dm */
        $dm = $c["directoryFactory"];
        $dm->addTable("article_categories");
        $gm = $dm->getManager("groups");
        return $gm;
    },
    "ArticlesManager" => function ($c) {
            $nm = new \Articles\Model\ArticlesManager();
            $nm->setCategoryManager($c["articleCategoriesManager"]);
            $fm = $c["fileManager"];
            $nm->setFileManager($fm);
            return $nm;
    },
];