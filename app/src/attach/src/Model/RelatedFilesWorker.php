<?php


namespace Akademiano\Attach\Model;


use Akademiano\Content\Files\Model\FilesWorker;

class RelatedFilesWorker extends FilesWorker
{
    const WORKER_NAME = "relatedFilesWorker";
    const FIELDS = ["title", "type", "sub_type", "path", "position", "size", "mime_type"];
}
