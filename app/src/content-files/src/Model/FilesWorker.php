<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\EntityOperator\Worker\NamedEntitiesWorker;

class FilesWorker extends NamedEntitiesWorker
{
    const WORKER_ID = 'filesWorker';
    const TABLE_ID = 14;
    const TABLE_NAME = "files";
    const FIELDS = ["type", "sub_type", "path", "position", "size", "mime_type"];
}
