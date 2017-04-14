<?php


namespace Akademiano\Config\FS;


use Akademiano\Config\Exception\ConfigFileNotReadException;

class ConfigFile
{
    const TYPE_LOCAL = 'local';
    const TYPE_GLOBAL = 'global';
    const TYPE_AUTO = 'auto';

    const EXT = "php";

    protected $id;
    protected $path;
    protected $type;
    protected $content;

    /**
     * ConfigFile constructor.
     * @param $path
     * @param $type
     */
    public function __construct($path, $type = self::TYPE_GLOBAL)
    {
        $this->setPath($path);
        $this->setType($type);
    }


    public function getId()
    {
        return $this->getType()."://".$this->getPath();
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    protected function read()
    {
        $path = $this->getPath();
        if (!is_readable($path)) {
            throw new ConfigFileNotReadException("$path");
        }
        return (array) include $path;
    }

    public function getContent()
    {
        if (null === $this->content) {
            $this->content = $this->read();
        }
        return $this->content;
    }
}
