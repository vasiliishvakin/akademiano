<?php


namespace Akademiano\Content\Tags\Api\v1;


use Akademiano\Api\v1\Entities\CompositeEntityApi;
use Akademiano\Content\Tags\Model\Tag;

class TagsApi extends CompositeEntityApi
{
    const ENTITY_CLASS = Tag::class;
    const API_ID = "tagsApi";
    const RELATIONS = [
        'dictionaries' => TagsDictionariesRelationsApi::API_ID,
    ];

    /** @var TagsDictionariesRelationsApi */
    protected $tagsDictionariesRelationsApi;

    /**
     * @return TagsDictionariesRelationsApi
     */
    public function getTagsDictionariesRelationsApi(): TagsDictionariesRelationsApi
    {
        return $this->tagsDictionariesRelationsApi;
    }

    /**
     * @param TagsDictionariesRelationsApi $tagsDictionariesRelationsApi
     */
    public function setTagsDictionariesRelationsApi(TagsDictionariesRelationsApi $tagsDictionariesRelationsApi): void
    {
        $this->tagsDictionariesRelationsApi = $tagsDictionariesRelationsApi;
    }
}
