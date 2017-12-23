<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class ArticleWorker extends PostgresWorker implements DelegatingInterface
{
    const TABLE_ID = 150;
    const TABLE_NAME = "articles";
    const EXPAND_FIELDS = ["title", "description", "content", "status"];

    use DelegatingTrait;
}
