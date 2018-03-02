<?php


namespace Akademiano\Content\Comments\Model;


use Akademiano\Attach\Model\LinkedFilesWorker;

class CommentFilesWorker extends LinkedFilesWorker
{
    const WORKER_NAME = "commentFilesWorker";
    const TABLE_ID = CommentsWorker::TABLE_ID + 1;
    const TABLE_NAME = "comment_files";

    public static function getEntityClassForMapFilter()
    {
        return CommentFile::class;
    }
}
