<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 17.01.16
 * Time: 2:36
 */

namespace Attach\Model\Parts;


use Attach\Model\FileManager;
use DeltaUtils\Object\Collection;

trait GetImagesTrait
{
    /** @var  \Attach\Model\File[]|Collection */
    protected $images;
    /** @var  \Attach\Model\File */
    protected $titleImage;
    /** @var  \Attach\Model\File[] */
    protected $otherImages;

    public function getImages()
    {
        if (is_null($this->images)) {
            $fm = $this->getFileManager();
            /** @var FileManager images */
            $this->images = $fm->getFilesForObject($this, ["type" => "image"]);
        }
        return $this->images;
    }

    public function getOtherImages()
    {
        if (null === $this->otherImages) {
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
