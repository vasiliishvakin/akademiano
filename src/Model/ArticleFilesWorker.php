<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Attach\Model\LinkedFilesWorker;

class ArticleFilesWorker extends LinkedFilesWorker
{
    const WORKER_NAME = "articleFilesWorker";
    const TABLE_ID = ArticlesWorker::TABLE_ID + 1;
    const TABLE_NAME = "article_files";

    public static function getEntityClassForMapFilter()
    {
        return ArticleFile::class;
    }
}
