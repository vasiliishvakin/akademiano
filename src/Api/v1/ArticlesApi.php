<?php


namespace Akademiano\Content\Articles\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Content\Articles\Model\Article;
use Akademiano\Entity\EntityInterface;

class ArticlesApi extends EntityApi
{
    const ENTITY_CLASS = Article::class;
    const API_ID = "articlesApi";
    const DEFAULT_ORDER = ["id" => "DESC"];

    /** @var  ArticleFilesApi */
    protected $filesApi;

    /**
     * @return ArticleFilesApi
     */
    public function getFilesApi()
    {
        return $this->filesApi;
    }

    /**
     * @param ArticleFilesApi $taskFilesApi
     */
    public function setFilesApi(ArticleFilesApi $filesApi)
    {
        $this->filesApi = $filesApi;
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
