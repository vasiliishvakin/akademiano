<?php


namespace Akademiano\Content\Comments\Model;


use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class CommentsWorker extends PostgresWorker implements DelegatingInterface
{
    const TABLE_ID = 18;
    const TABLE_NAME = "comments";
    const EXPAND_FIELDS = ["title", "description", "content", "entity"];

    use DelegatingTrait;
}
