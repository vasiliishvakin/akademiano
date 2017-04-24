<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\Object\Prototype\StringableInterface;

class File implements StringableInterface
{
    protected $path;

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

    public function __toString()
    {
        return $this->getPath();
    }

}
