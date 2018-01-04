<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Attach\Model\LinkedFilesWorker;

class ArticleFilesWorker extends LinkedFilesWorker
{
    const WORKER_NAME = "articleFilesWorker";
    const TABLE_ID = ArticlesWorker::TABLE_ID + self::TABLE_ID_INC;
    const TABLE_NAME = "article_files";
    const EXPAND_FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type", self::LINKED_ENTITY_FIELD];

}
