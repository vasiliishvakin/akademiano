<?php

namespace Articles\Model;

use Attach\Model\Parts\ImagesTrait;
use DeltaPhp\Operator\Entity\ContentEntity;
use DeltaPhp\Operator\Entity\ContentEntityInterface;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Command\RelationLoadCommand;
use DeltaPhp\Operator\EntityOperator;
use DeltaUtils\Object\Collection;
use DeltaPhp\TagsDictionary\Entity\Tag;
use Attach\Model\ImageFileEntity;

/**
 * Class Article
 * @package Articles
 */
class Article extends ContentEntity implements ArticleInterface, ContentEntityInterface, EntityInterface, DelegatingInterface
{

    use DelegatingTrait;
    use ImagesTrait;

    /** @var  Collection|Tag[] */
    protected $tags;

    /**
     * @return \Attach\Model\ImageFileEntity[]|Collection
     */
    public function getImages()
    {
        if (null === $this->images) {
            $command = new RelationLoadCommand(ArticleImageRelation::class, $this);
            $this->images = $this->delegate($command);
            $this->images->usort(function (ImageFileEntity $imageA, ImageFileEntity $imageB) {
                if ($imageA->isMain()) {
                    return -1;
                } elseif ($imageB->isMain()) {
                    return 1;
                } else {
                    if ($imageA->getOrder() === $imageB->getOrder()) {
                        return 0;
                    }
                    return ($imageA->getOrder() < $imageB->getOrder()) ? -1 : 1;
                }
            });
        }
        return $this->images;
    }


    /**
     * @return Collection
     * @deprecated
     */
    public function getCategories()
    {
        return $this->getTags();
    }


    public function setTags($tags)
    {
        $this->tags = $tags;
    }


    /**
     * @return Collection|Tag[]
     */
    public function getTags()
    {
        if (is_array($this->tags)) {
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $this->tags = $operator->find(Tag::class, ["id" => $this->tags]);
        }

        if (null === $this->tags) {
            $command = new RelationLoadCommand(ArticleTagRelation::class, $this);
            $this->tags = $this->delegate($command);
        }
        return $this->tags;
    }
}
