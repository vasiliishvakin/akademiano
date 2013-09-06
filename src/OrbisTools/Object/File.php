<?php
namespace OrbisTools\Object;

use OrbisTools\FileSystem as FS;
use \SplFileInfo;

class File
{
    protected $path;
    protected $type;
    protected $name;
    protected $mime;
    protected $fileInfo;
    protected $fileOpen;

    function __construct($path, $name = null, $type = null, $mime = null)
    {
        $this->path = $path;

        if (!is_null($type) && ($type === FS::FST_DIR || $type === FS::FST_FILE)) {
            $this->type = $type;
        }

        if (!is_null($name)) {
            $this->name = $name;
        }

        if (!is_null($mime)) {
            $this->mime = $mime;
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getName()
    {
        if (is_null($this->name)) {

        }
        return $this->name;
    }

    public function getType()
    {
        if (is_null($this->type)) {
            if (is_dir($this->getPath())) {
                $this->type = FS::FST_DIR;
            } elseif (is_file($this->getPath())) {
                $this->type = FS::FST_FILE;
            }
        }
        return $this->type;
    }

    public function getMime()
    {
        if (is_null($this->mime)) {
            $this->mime = FS::getFileType($this->path);
        }
        return $this->mime;
    }
    
    public function isDir()
    {
        return $this->getType() === FS::FST_DIR;
    }

    public function isFile()
    {
        return $this->getType() === FS::FST_FILE;
    }

    /**
     * @return SplFileInfo
     */
    public function getInfo()
    {
        if (is_null($this->fileInfo)) {
            $this->fileInfo = new SplFileInfo($this->getPath());
        }
        return $this->fileInfo;
    }

    /**
     * @param string $openMode
     * @return \SplFileObject
     */
    public function open($openMode = 'r')
    {
        if (is_null($this->fileOpen)) {
            $this->fileOpen = $this->getInfo()->openFile($openMode);
        }
        return $this->fileOpen;
    }

}