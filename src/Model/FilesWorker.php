<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\EntityOperator\Worker\PostgresWorker;

class FilesWorker extends PostgresWorker
{
    const TABLE_ID_INC = 1;
    const TABLE_ID = 13;
    const TABLE_NAME = "files_linked";
    const EXPAND_FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type", "entity"];
}
