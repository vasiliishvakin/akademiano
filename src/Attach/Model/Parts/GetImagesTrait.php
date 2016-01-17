<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 17.01.16
 * Time: 2:36
 */

namespace Attach\Model\Parts;


trait GetImagesTrait
{
    /** @var  \Attach\Model\File[] */
    protected $images;

    public function getImages()
    {
        if (is_null($this->images)) {
            $fm = $this->getFileManager();
            $this->images = $fm->getFilesForObject($this, ["type" => "image"]);
        }
        return $this->images;
    }

    public function getOtherImages()
    {
        return $this->getImages()->slice(1);
    }

    public function getTitleImage()
    {
        return $this->getImages()->first();
    }
}
