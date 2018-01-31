<?php


namespace Akademiano\Attach\Model;


use Akademiano\Content\Files\Model\FilesWorker;

class LinkedFilesWorker extends FilesWorker
{
    const LINKED_ENTITY_FIELD = 'entity';
    const WORKER_NAME = "linkedFilesWorker";
    const TABLE_ID = 15;
    const TABLE_NAME = "linked_files";
    const EXT_ENTITY_FIELDS = [self::LINKED_ENTITY_FIELD];
    const FIELDS = [self::LINKED_ENTITY_FIELD];
}
