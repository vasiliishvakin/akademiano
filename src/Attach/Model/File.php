<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Attach\Model;


use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;
use UUID\Model\Parts\UuidhasInterface;
use UUID\Model\Parts\UuidTrait;
use UUID\Model\Parts\UuidFactoryTrait;

class File extends AbstractEntity implements EntityInterface, UuidhasInterface
{
    use UuidFactoryTrait;
    use UuidTrait;

    protected $section;
    protected $object;
    protected $type;
    protected $name;
    protected $description;
    protected $path;
    protected $rootUri;

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function getFileName()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    public function getFileDirectory()
    {
        return pathinfo($this->getPath(), PATHINFO_DIRNAME);
    }

    /**
     * @return mixed
     */
    public function getRootUri()
    {
        return $this->rootUri;
    }

    /**
     * @param mixed $rootUrl
     */
    public function setRootUri($rootUrl)
    {
        $this->rootUri = $rootUrl;
    }


    /** @deprecated */
    public function getUri($template = null)
    {
        return $this->getUrl($template);
    }

    public function getUrl($template = null)
    {
        $fileDir = $this->getFileDirectory();
        if (strpos($fileDir, "public/") === 0) {
            $fileDir = substr($fileDir, 7);
        }
        if (null !== $template) {
            $dirs = explode("/", $fileDir);

            if (count($dirs) === 2) {
                $fileDir = $template . "/" . $fileDir;
            } else {
                array_splice($dirs, -2, 0, $template);
                $fileDir = implode("/", $dirs);
            }
        }

        return $this->getRootUri() . "/" . $fileDir . "/" . $this->getFileName();
    }
}
