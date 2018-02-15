<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Api\v1\Entities\CompositeEntityApi;
use Akademiano\Content\Articles\Model\Article;
use Akademiano\Entity\EntityInterface;

class ArticlesApi extends CompositeEntityApi
{
    const ENTITY_CLASS = Article::class;
    const API_ID = "articlesApi";
    const DEFAULT_ORDER = ["id" => "DESC"];
    const RELATIONS = [
        'tags' => TagsArticlesRelationsApi::API_ID,
    ];

    /** @var  ArticleFilesApi */
    protected $filesApi;

    /** @var TagsArticlesRelationsApi */
    protected $tagsArticlesRelationsApi;


    public function getFilesApi():ArticleFilesApi
    {
        return $this->filesApi;
    }

    public function setFilesApi(ArticleFilesApi $filesApi)
    {
        $this->filesApi = $filesApi;
    }

    /**
     * @return TagsArticlesRelationsApi
     */
    public function getTagsArticlesRelationsApi(): TagsArticlesRelationsApi
    {
        return $this->tagsArticlesRelationsApi;
    }

    /**
     * @param TagsArticlesRelationsApi $tagsArticlesRelationsApi
     */
    public function setTagsArticlesRelationsApi(TagsArticlesRelationsApi $tagsArticlesRelationsApi): void
    {
        $this->tagsArticlesRelationsApi = $tagsArticlesRelationsApi;
    }

    public function deleteEntity(EntityInterface $entity)
    {
        if ($entity instanceof Article) {
            foreach ($entity->getFiles() as $file) {
                $this->getFilesApi()->deleteEntity($file);
            }
        }
        return parent::deleteEntity($entity);
    }
}
