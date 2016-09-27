<?php


namespace Articles\Model;


use DeltaPhp\Operator\Entity\RelationEntity;
use DeltaPhp\TagsDictionary\Entity\Tag;

class ArticleTagRelation extends RelationEntity
{
    public function __construct()
    {
        $this->setFirstClass(Article::class);
        $this->setSecondClass(Tag::class);
    }
}
