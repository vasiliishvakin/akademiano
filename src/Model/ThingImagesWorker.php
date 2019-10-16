<?php


namespace Akademiano\Content\Knowledgebase\It\Model;


use Akademiano\Content\Articles\Model\ArticleImagesWorker;

class ThingImagesWorker extends ArticleImagesWorker
{
    public const WORKER_ID = "kbThingImagesWorker";
    public const TABLE_NAME = "content_kb_thing_images";

    public const ENTITY = ThingImage::class;
}
