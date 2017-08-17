<?php


namespace Akademiano\Attach\Model;


use Akademiano\Content\Files\Model\FilesWorker;

class LinkedFilesWorker extends FilesWorker
{
    const WORKER_NAME = "linkedFilesWorker";
    const TABLE_ID = FilesWorker::TABLE_ID + self::TABLE_ID_INC;
    const TABLE_NAME = "linked_files";
    const LINKED_ENTITY_FIELD = "entity";
    const EXPAND_FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type", self::LINKED_ENTITY_FIELD];
}
