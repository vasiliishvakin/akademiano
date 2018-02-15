<?php


namespace Akademiano\Content\Tags\Model;


class TagDictionaryRelation extends TagRelation
{
    const SECOND_CLASS = Dictionary::class;

    public function getTag():Tag
    {
        return $this->getFirst();
    }

    public function getDictionary()
    {
        return $this->getSecond();
    }
}
