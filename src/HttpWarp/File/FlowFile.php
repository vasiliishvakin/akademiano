<?php

namespace HttpWarp\File;


use DeltaUtils\FileSystem;
use HttpWarp\File\Parts\FileProperties;

class FlowFile implements FileInterface
{
    use FileProperties;

    protected $name;
    protected $type;
    protected $size;
    protected $path;

    function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
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
    public function getMimeType()
    {
        if (is_null($this->type)) {
            $this->type = FileSystem::getFileType($this->getPath());
        }
        return $this->type;
    }

    public function mv($path)
    {
        $result = rename($this->getPath(), $path);
        if (!$result) {
            return false;
        }
        $this->path = $path;
        return $this->getPath();
    }
}
