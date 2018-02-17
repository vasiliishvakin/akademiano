<?php

namespace Akademiano\Content\Articles\Controller;


use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Content\Articles\Api\v1\ArticlesApi;
use Akademiano\Content\Articles\Api\v1\TagsArticlesRelationsApi;
use Akademiano\Content\Articles\Model\TagArticleRelation;
use Akademiano\Content\Articles\RoutesStore;
use Akademiano\Content\Tags\Api\v1\TagsApi;
use Akademiano\Entity\Uuid;
use Akademiano\EntityOperator\Ext\Controller\AkademianoCompositeEntityController;
use Akademiano\HttpWarp\Exception\NotFoundException;

/**
 * @method ArticlesApi getEntityApi()
 */
class IndexController extends AkademianoCompositeEntityController
{
    const ENTITY_OPSR_STORE_CLASS = RoutesStore::class;
    const ENTITY_API_ID = ArticlesApi::API_ID;
    const FORM_FILES_FIELD = "files";


    public function getListCriteria()
    {
        return [];
    }

    public function getTagsApi():TagsApi
    {
        return $this->getDiContainer()[TagsApi::API_ID];
    }

    public function tagAction(array $params = [])
    {
        $api = $this->getEntityApi();
        $tagsApi = $this->getTagsApi();

        $id = $params['id'];
        $id = Uuid::normalize($id);
        if (!$id) {
            throw new NotFoundException();
        }
        $tag = $tagsApi->get($id)->getOrThrow(new NotFoundException());

        $relationsApi = $this->getRelatedEntityApi(TagsArticlesRelationsApi::API_ID);
        /** @var ItemsPage $relations */
        $relations = $relationsApi->find(
            [TagArticleRelation::FIRST_FIELD => $tag],
            $this->getPage(),
            $this->getListOrder(),
            $this->getItemsPerPage()
        );

        $articles = $relations->getItems()->lists(TagArticleRelation::SECOND_FIELD);
        $items = new ItemsPage($articles, $relations->getPageMetadata());
        return [
            'tag' => $tag,
            "items" => $items,
        ];
    }
}
