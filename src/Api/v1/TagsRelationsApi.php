<?php


namespace Akademiano\Content\Tags\Api\v1;


use Akademiano\Api\v1\Entities\RelationEntityApi;
use Akademiano\Content\Tags\Model\TagRelation;

class TagsRelationsApi extends RelationEntityApi
{
    const ENTITY_CLASS = TagRelation::class;
    const API_ID = "tagsRelationsApi";
}
