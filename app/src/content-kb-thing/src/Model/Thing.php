<?php


namespace Akademiano\Content\Knowledgebase\Thing\Model;


use Akademiano\Content\Articles\Model\Article;
use Akademiano\Utils\Object\Collection;

class Thing extends Article
{
    const ENTITY_FILES_CLASS = ThingImage::class;

    public function getTags(): Collection
    {
        return new Collection([]);
    }
}
