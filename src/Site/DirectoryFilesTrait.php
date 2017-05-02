<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\Exception\FileNotFoundException;
use Akademiano\Utils\FileSystem;

trait DirectoryFilesTrait
{
    use DirectoryPathTrait;

    protected $files = [];

    protected $filesList = [];


    protected function createFile($path)
    {
        return new File($path);
    }

    public function getFile($fileName)
    {
        if (!array_key_exists($fileName, $this->files)) {
            $filePath = $this->getPath() . DIRECTORY_SEPARATOR . $fileName;
            if (!file_exists($filePath) || !is_readable($filePath)
                || !FileSystem::isFileInDir($this->getPath(), $filePath)
            ) {
                $this->files[$fileName] = null;
            } else {
                $this->files[$fileName] = $this->createFile($filePath);
            }
        }
        return $this->files[$fileName];
    }

    public function getFileOrThrow($fileName)
    {
        $file = $this->getFile($fileName);
        if (!$file) {
            throw new FileNotFoundException(sprintf('File "%s" not found in "%s"', $fileName, $this->getPath()));
        }
        return $file;
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
            $path = $this->getPath();
            if (null !== $subPath) {
                $path = realpath($path . DIRECTORY_SEPARATOR . $subPath);
                if (!$path || !is_dir($path) || !FileSystem::inDir($this->getPath(), $path)) {
                    throw new FileNotFoundException(sprintf('Directory %s not exist'), $path);
                }
            }
            $this->filesList[$key] = FileSystem::getItems($path, $resultType, $itemType, $level, $showHidden);
        }
        return $this->filesList[$key];
    }
}
