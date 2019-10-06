<?php


namespace Akademiano\Content\Knowledgebase\Thing\Model;


use Akademiano\Content\Articles\Model\ArticlesWorker;

class ThingWorker extends ArticlesWorker
{
    public const WORKER_ID = 'kbThingWorker';
    public const TABLE_NAME = "content_kb_things";

    public const ENTITY = Thing::class;
}