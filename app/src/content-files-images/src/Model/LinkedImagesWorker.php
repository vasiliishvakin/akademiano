<?php


namespace Akademiano\Content\Files\Images\Model;


use Akademiano\Attach\Model\LinkedFilesWorker;

class LinkedImagesWorker extends LinkedFilesWorker
{
    const WORKER_NAME = "linkedImagesWorker";
    const TABLE_ID = 17;
    const TABLE_NAME = "linked_images";
    const FIELDS = ['main', 'order'];

    public static function getEntityClassForMapFilter()
    {
        return LinkedImage::class;
    }
}
