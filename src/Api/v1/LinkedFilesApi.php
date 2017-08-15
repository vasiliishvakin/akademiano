<?php


namespace Akademiano\Attach\Api\v1;

use Akademiano\Attach\Model\LinkedFile;
use \Akademiano\Content\Files\Api\v1\FilesApi;

class LinkedFilesApi extends FilesApi
{
    const API_ID = "linkedFilesApi";
    const ENTITY_CLASS = LinkedFile::class;
}
