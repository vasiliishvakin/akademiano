<?php


namespace Akademiano\Content\Comments\Model;


use Akademiano\Attach\Model\EntityFileRelationWorker;
use Akademiano\Attach\Model\LinkedFilesWorker;

class CommentFilesWorker extends LinkedFilesWorker
{
    const WORKER_NAME = "commentFilesWorker";
    const TABLE_ID = 20; //EntityFileRelationWorker::TABLE_ID + self::TABLE_ID_INC;
    const TABLE_NAME = "comment_files";
    const EXPAND_FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type", self::LINKED_ENTITY_FIELD];
}
