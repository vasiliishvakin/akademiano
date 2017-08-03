<?php


namespace Akademiano\Attach\Model;


use Akademiano\EntityOperator\Worker\PostgresWorker;

class FilesWorker extends PostgresWorker
{
    const TABLE_ID_INC = 1;
    const TABLE_ID = 12;
    const TABLE_NAME = "files";
    const EXPAND_FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type"];
}
