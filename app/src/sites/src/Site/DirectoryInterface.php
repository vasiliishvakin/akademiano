<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\Utils\FileSystem;

interface DirectoryInterface extends StringableInterface
{
    public function getPath();

    /**
     * @param $fileName
     * @return File
     */
    public function getFile($fileName);

    public function getFilesList(
        $path,
        $resultType = FileSystem::LIST_SCALAR,
        $itemType = FileSystem::FST_ALL,
        $level = false,
        $showHidden = false
    );

}
