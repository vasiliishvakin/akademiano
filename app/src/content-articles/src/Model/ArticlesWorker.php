<?php


namespace Akademiano\Content\Articles\Model;


use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Worker\ContentEntitiesWorker;
use Akademiano\EntityOperator\Worker\PostgresEntityWorker;
use Akademiano\Operator\Command\PreCommand;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\Operator\WorkersMap\Filter\ValueClassExtractor;
use Akademiano\Utils\ArrayTools;

class ArticlesWorker extends ContentEntitiesWorker implements DelegatingInterface
{
    const WORKER_ID = 'articlesWorker';
    const TABLE_ID = 150;
    const TABLE_NAME = "articles";
    const FIELDS = ['tags'];
    const UNSAVED_FIELDS = ['tags'];

    public static function getEntityClassForMapFilter()
    {
        return Article::class;
    }
}
