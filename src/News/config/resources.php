<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "NewsManager" => function ($c) {
            $nm = new \News\Model\NewsManager();
            $df = $c["directoryFactory"];
            $cm = $df->getManager("categories");
            $nm->setCategoryManager($cm);
            $fm = $c["fileManager"];
            $nm->setFileManager($fm);
            return $nm;
    },
];