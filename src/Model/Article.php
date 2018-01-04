<?php

namespace Akademiano\Content\Articles\Model;


use Akademiano\Entity\ContentEntity;
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
            $this->files = $this->getOperator()->find(static::ENTITY_FILES_CLASS, $criteria);
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

}
