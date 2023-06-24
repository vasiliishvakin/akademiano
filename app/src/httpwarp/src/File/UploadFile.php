<?php

namespace Akademiano\HttpWarp\File;


use Akademiano\Utils\FileSystem;
use Akademiano\HttpWarp\File\Parts\FileProperties;

class UploadFile implements FileInterface
{
    use FileProperties;

    protected $name;
    protected $mimeType;
    protected $type;
    protected $subType;
    protected $size;
    protected $path;
    protected $error;
    protected $isMoved = false;


    public function __construct($name, $path, $error)
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

    public function getFullPath()
    {
        return $this->getPath();
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
    public function getMimeType()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = FileSystem::getFileType($this->getPath());
        }
        return $this->mimeType;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if (null === $this->type) {
            $mime = $this->getMimeType();
            $types = explode("/", $mime);
            $this->type = $types[0];
            $this->subType = $types[1];
        }
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSubType()
    {
        if (null === $this->subType) {
            $mime = $this->getMimeType();
            $types = explode("/", $mime);
            $this->type = $types[0];
            $this->subType = $types[1];
        }
        return $this->subType;
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
}
