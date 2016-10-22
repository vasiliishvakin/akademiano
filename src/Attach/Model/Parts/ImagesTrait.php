<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 17.01.16
 * Time: 2:36
 */

namespace Attach\Model\Parts;


use Attach\Model\EntityImageRelation;
use Attach\Model\ImageFileEntity;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\RelationLoadCommand;

/**
 * Class GetImagesTrait
 * @package Attach\Model\Parts
 */
trait ImagesTrait
{
    /** @var  \Attach\Model\ImageFileEntity[]|Collection */
    protected $images;
    /** @var  \Attach\Model\ImageFileEntity */
    protected $titleImage;
    /** @var  \Attach\Model\ImageFileEntity[] */
    protected $otherImages;

    /**
     * @return \Attach\Model\ImageFileEntity[]|Collection
     */
    public function getImages()
    {
        if (null === $this->images) {
            $command = new RelationLoadCommand(EntityImageRelation::class, $this);
            /** @var Collection $images */
            $images = $this->delegate($command);
            $images->usort(function (ImageFileEntity $imageA, ImageFileEntity $imageB) {
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
            $this->images = $images;
        }
        return $this->images;
    }

    public function getOtherImages()
    {
        if ($this->getImages()->isEmpty()) {
            $this->otherImages = new Collection();
        } elseif (null === $this->otherImages) {
            $titleImage = $this->getTitleImage();
            if ($titleImage->isMain()) {
                $otherImages = [];
                foreach ($this->getImages() as $image) {
                    if ($image !== $titleImage) {
                        $otherImages[] = $image;
                    }
                }
                $this->otherImages = new Collection($otherImages);
            } else {
                $this->otherImages = $this->getImages()->slice(1);
            }
        }
        return $this->otherImages;
    }

    public function getTitleImage()
    {
        if (null === $this->titleImage) {
            foreach ($this->getImages() as $image) {
                if ($image->isMain()) {
                    $this->titleImage = $image;
                    break;
                }
            }
            if (null === $this->titleImage) {
                $this->titleImage = $this->getImages()->first();

            }
        }
        return $this->titleImage;
    }
}
