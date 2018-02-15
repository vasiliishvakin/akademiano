<?php

namespace Akademiano\Content\Articles\Model;


use Akademiano\Content\Tags\Model\Tag;
use Akademiano\Entity\ContentEntity;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\Object\Collection;

class Article extends ContentEntity
{
    const ENTITY_FILES_CLASS = ArticleFile::class;

    use OwneredTrait;

    /** @var  ArticleFile[]|Collection */
    protected $files;

    /** @var  ArticleFile[]|Collection */
    protected $images;

    /** @var Tag[]|Collection */
    protected $tags;


    /**
     * @return Collection|ArticleFile[]
     */
    public function getFiles()
    {
        if (!$this->files instanceof Collection) {
            if (is_array($this->files)) {
                $criteria["id"] = $this->files;
            } else {
                $criteria = ["entity" => $this];
            }
            $command = (new FindCommand(ArticleFile::class))->setCriteria($criteria);
            $this->files = $this->delegate($command);
        }
        return $this->files;
    }


    public function getImages()
    {
        if (!$this->images instanceof Collection) {
            $files = $this->getFiles();
            $images = [];
            foreach ($files as $file) {
                if ($file->isImage()) {
                    $images[$file->getId()->getInt()] = $file;
                }
            }
            $this->images = new Collection($images);
        }
        return $this->images;
    }

    public function getFirstImage()
    {
        return $this->getImages()->first();
    }


    public function getOtherImages()
    {
        return $this->getImages()->slice();
    }

    /**
     * @return Tag[]|Collection
     */
    public function getTags():Collection
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
