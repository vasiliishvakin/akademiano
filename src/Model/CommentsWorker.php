<?php


namespace Akademiano\Content\Comments\Model;


use Akademiano\EntityOperator\Worker\ContentEntitiesWorker;

class CommentsWorker extends ContentEntitiesWorker
{
    const LINKED_ENTITY_FIELD = 'entity';

    const WORKER_NAME = "commentsWorker";
    const TABLE_ID = 18;
    const TABLE_NAME = "comments";
    const FIELDS = [self::LINKED_ENTITY_FIELD];
    const EXT_ENTITY_FIELDS = [self::LINKED_ENTITY_FIELD];
}
