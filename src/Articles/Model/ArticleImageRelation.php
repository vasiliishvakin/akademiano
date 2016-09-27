<?php


namespace Articles\Model;

use Attach\Model\EntityImageRelation;
use Attach\Model\ImageFileEntity;

class ArticleImageRelation extends EntityImageRelation
{
    public function __construct()
    {
        $this->setFirstClass(Article::class);
        $this->setSecondClass(ImageFileEntity::class);
    }
}
