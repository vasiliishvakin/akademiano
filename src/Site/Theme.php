<?php


namespace Akademiano\Sites\Site;

use Akademiano\Utils\FileSystem;
use Akademiano\HttpWarp\Exception\AccessDeniedException;

class Theme extends Directory
{
    protected $name;

    /**
     * Theme constructor.
     * @param $name
     */
    public function __construct($name, $path)
    {
        $this->setName($name);
        $this->setInternalPath($path);
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    protected function getGlobalPath()
    {
        return $this->getInternalPath();
    }

    protected function createFile($path)
    {
        return parent::createFile($path);
    }

    public function __toString()
    {
        return $this->getInternalPath();
    }

    public function getFile($relativeFilePath)
    {
        if (!array_key_exists($relativeFilePath, $this->files)) {
            $filePath = $this->getGlobalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
            if (!file_exists($filePath)) {
                $fileInternalPath = $this->getInternalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
                if (!file_exists($fileInternalPath) || !is_readable($fileInternalPath)) {
                    if (FileSystem::isFileInDir($this->getInternalPath(), $fileInternalPath)) {
                        throw new AccessDeniedException(sprintf('Not allow access to file "%s"'));
                    }
                    $this->files[$relativeFilePath] = null;
                    return null;
                }
            }
            $this->files[$relativeFilePath] = $this->createFile($fileInternalPath);
        }
        return $this->files[$relativeFilePath];
    }


}
