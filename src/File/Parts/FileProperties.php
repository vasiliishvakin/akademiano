<?php


namespace Akademiano\HttpWarp\File\Parts;

use Akademiano\Utils\FileSystem;

trait FileProperties
{
    protected $isImage;
    protected $ext;

    abstract public function getPath();
    abstract public function getName();

    public function checkType($type)
    {
        return FileSystem::checkType($this->getPath(), $type);
    }

    public function isImage()
    {
        if (is_null($this->isImage)) {
            $this->isImage = (bool)$this->checkType(FileSystem::FST_IMAGE);
        }
        return $this->isImage;
    }

    public function getExt()
    {
        if (is_null($this->ext)) {
            $imageType = null;
            if ($this->isImage()) {
                $imageType = getimagesize($this->getPath());
                $imageType = isset($imageType[2]) ? $imageType[2] : null;
            }
            if ($imageType) {
                $ext = image_type_to_extension($imageType, false);
            } else {
                $ext = pathinfo($this->getName(), PATHINFO_EXTENSION);
            }
            $ext = strtolower($ext);
            switch ($ext) {
                case "jpeg" :
                    $ext = "jpg";
                    break;
            }
            $this->ext = $ext;
        }
        return $this->ext;
    }

}
