<?php


namespace Akademiano\Sites\Site;


use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\Utils\FileSystem;

class FlowDirectory extends Directory implements FlowDirectoryInterface
{
    protected $internalPath;
    protected $globalPath;
    protected $existGlobalPath = false;

    public function __construct($internalPath, $globalPath)
    {
        $this->setInternalPath($internalPath);
        $this->setGlobalPath($globalPath);
    }

    /**
     * @return mixed
     */
    public function getInternalPath()
    {
        return $this->internalPath;
    }

    /**
     * @param mixed $internalPath
     */
    public function setInternalPath($internalPath)
    {
        $this->internalPath = $internalPath;
    }

    public function existGlobalPath()
    {
        if (null !== $this->existGlobalPath) {
            $this->existGlobalPath = is_dir($this->globalPath);
        }
        return $this->existGlobalPath;
    }

    /**
     * @return mixed
     */
    public function getGlobalPath()
    {
        if (!$this->existGlobalPath()) {
            if (!is_dir($this->globalPath)) {
                $created = mkdir($this->globalPath, 0750);
                if (!$created) {
                    throw new \RuntimeException(sprintf('Could not create global storage directory "%s"', $this->globalPath));
                }
                $this->existGlobalPath = true;
            }
        }
        return $this->globalPath;
    }

    /**
     * @param mixed $globalPath
     */
    public function setGlobalPath($globalPath)
    {
        $this->globalPath = $globalPath;
    }

    public function setPath($path)
    {
        $this->setInternalPath($path);
    }

    public function getPath()
    {
        return $this->getGlobalPath();
    }

    /**
     * @param $relativeFilePath
     * @return File|null
     * @throws AccessDeniedException
     */
    public function getFile($relativeFilePath)
    {
        if (!array_key_exists($relativeFilePath, $this->files)) {
            $filePath = $this->getGlobalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
            if (!file_exists($filePath)) {
                $fileInternalPath = $this->getInternalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
                if (!file_exists($fileInternalPath) || !is_readable($fileInternalPath)) {
                    $filePath = null;
                } else {
                    if (!FileSystem::isFileInDir($this->getInternalPath(), $fileInternalPath)) {
                        throw new AccessDeniedException(sprintf('Not allow access to file "%s"', $relativeFilePath));
                    }
                    FileSystem::copyOrThrow($fileInternalPath, $filePath);
                }
            }
            $this->files[$relativeFilePath] = $filePath ? $this->createFile($filePath) : null;
        }
        return $this->files[$relativeFilePath];
    }

    public function getFilesList(
        $subPath = null,
        $resultType = FileSystem::LIST_SCALAR,
        $itemType = FileSystem::FST_ALL,
        $level = false,
        $showHidden = false
    )
    {
        $key = sprintf("%s-%s-%s-%s-%s", $subPath, $resultType, $itemType, $level, $showHidden);

        if (!array_key_exists($key, $this->filesList)) {
            //files global
            $filesGlobal = [];
            $path = $this->getGlobalPath();
            if (null !== $subPath) {
                $path = realpath($path . DIRECTORY_SEPARATOR . $subPath);
            }
            if (!$path || !is_dir($path) || !FileSystem::inDir($this->getPath(), $path)) {
                $filesGlobal = [];
            } else {
                $filesGlobal = FileSystem::getItems($path, $resultType, $itemType, $level, $showHidden);
            }

            //files internal
            $filesInternal = [];
            $path = $this->getInternalPath();
            if (null !== $subPath) {
                $path = realpath($path . DIRECTORY_SEPARATOR . $subPath);
            }
            if (!$path || !is_dir($path) || !FileSystem::inDir($this->getPath(), $path)) {
                $filesInternal = [];
            } else {
                $filesInternal = FileSystem::getItems($path, $resultType, $itemType, $level, $showHidden);
            }

            $filesList = array_merge($filesGlobal, $filesInternal);
            $filesList = array_unique($filesList);
            $this->filesList[$key] = $filesList;
        }
        return $this->filesList[$key];
    }


    public function __toString()
    {
        return $this->getGlobalPath();
    }
}
