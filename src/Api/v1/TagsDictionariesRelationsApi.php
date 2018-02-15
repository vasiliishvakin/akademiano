<?php


namespace Akademiano\Content\Tags\Api\v1;


use Akademiano\Content\Tags\Model\TagDictionaryRelation;
use Akademiano\Content\Tags\Model\TagsDictionariesRelationWorker;

class TagsDictionariesRelationsApi extends TagsRelationsApi
{
    const API_ID = 'tagsDictionariesRelationsApi';
    const ENTITY_CLASS = TagDictionaryRelation::class;

    const FIELD_FIRST = TagsDictionariesRelationWorker::FIELD_FIRST;
    const FIELD_SECOND = TagsDictionariesRelationWorker::FIELD_SECOND;
}
