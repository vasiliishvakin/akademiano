<?php


namespace Articles\Model;


use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Worker\PostgresWorker;

class ArticleWorker extends PostgresWorker implements DelegatingInterface
{
    use DelegatingTrait;

    public function __construct()
    {
        $this->setTable("articles");
        $this->addFields(["title", "description", "content"]);
    }

}
