<?php

namespace Akademiano\Content\Articles\Model;


use Akademiano\Content\Tags\Model\Tag;
use Akademiano\Entity\ContentEntity;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\Object\Collection;

class Article extends ContentEntity
{
    const ENTITY_FILES_CLASS = ArticleImage::class;

    use OwneredTrait;

    /** @var  ArticleImage[]|Collection */
    protected $images;

    /** @var Tag[]|Collection */
    protected $tags;

    protected $mainImage;

    /**
     * @return Collection|ArticleImage[]
     */
    public function getImages()
    {
        if (!$this->images instanceof Collection) {
            if (is_array($this->images)) {
                $criteria["id"] = $this->images;
            } else {
                $criteria = ["entity" => $this];
            }
            $command = (new FindCommand(ArticleImage::class))
                ->setCriteria($criteria)
                ->setOrderBy(['main'=>'DESC', 'order' => 'ASC']);
            $this->images = $this->delegate($command);
        }
        return $this->images;
    }

    public function getMainImage(): ?ArticleImage
    {
        if (null === $this->mainImage) {
            if ($this->getImages()->isEmpty()) {
                return null;
            }
            $main = $this->getImages()->filter('main', true)->first();
            if (!$main) {
                $main = $this->getFirstImage();
            }
            $this->mainImage = $main;
        }
        return $this->mainImage;
    }

    protected function getFirstImage()
    {
        return $this->getImages()->first();
    }

    public function getOtherImages()
    {
        if ($this->getImages()->isEmpty()) {
            return [];
        }
        return $this->getImages()->filter(function (ArticleImage $item) {
            return $item->getInt();
        }, $this->getMainImage()->getInt(), '!==');
    }

    /**
     * @return Tag[]|Collection
     */
    public function getTags(): Collection
    {
        if (!$this->tags instanceof Collection) {
            if (null === $this->tags) {
                /** @var Collection $relations */
                $relations = $this->delegate((new FindCommand(TagArticleRelation::class))->setCriteria([TagsArticlesRelationsWorker::FIELD_SECOND => $this]));
                $this->tags = $relations->lists(TagsArticlesRelationsWorker::FIELD_FIRST);
            } else {
                $this->tags = $this->delegate((new FindCommand(ArticleTag::class))->setCriteria(['id' => $this->tags]));
            }
        }
        return $this->tags;
    }

    public function setTags(iterable $tags): void
    {
        if (count($tags) === 0) {
            $this->tags = new Collection([]);
        } else {
            $this->tags = $tags;
        }
    }
}
