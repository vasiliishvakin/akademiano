<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace HttpWarp\File;


use DeltaUtils\FileSystem;

class UploadFile implements FileInterface
{
    protected $name;
    protected $type;
    protected $size;
    protected $path;
    protected $error;
    protected $isMoved = false;
    protected $isImage;
    protected $ext;

    function __construct($name, $path, $error)
    {
        $this->name = $name;
        $this->path = $path;
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        if (is_null($this->size)) {
            $this->size = filesize($this->getPath());
        }
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if (is_null($this->type)) {
            $this->type = FileSystem::getFileType($this->getPath());
        }
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isMoved()
    {
        return $this->isMoved;
    }

    public function mv($path)
    {
        if (!$this->isMoved()) {
            $file = $this->getPath();
            $result = move_uploaded_file($file, $path);
            $this->isMoved = $result;
        } else {
            $result = rename($this->getPath(), $path);
        }
        if (!$result) {
            return false;
        }
        $this->path = $path;
        return $this->getPath();
    }

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