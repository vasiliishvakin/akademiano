<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace HttpWarp\File;


use DeltaUtils\FileSystem;
use HttpWarp\File\Parts\FileProperties;

class UploadFile implements FileInterface
{
    use FileProperties;

    protected $name;
    protected $type;
    protected $size;
    protected $path;
    protected $error;
    protected $isMoved = false;


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
}
