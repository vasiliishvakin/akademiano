<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\Object\Prototype\StringableInterface;

class File implements StringableInterface
{
    protected $path;

    protected $content;

    /**
     * File constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->setPath($path);
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
    public function getContent()
    {
        if (null === $this->content) {
            $this->content = file_get_contents($this->getPath());
        }
        return $this->content;
    }

    public function __toString()
    {
        return $this->getPath();
    }

}
