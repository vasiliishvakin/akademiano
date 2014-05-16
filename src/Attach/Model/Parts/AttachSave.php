<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Attach\Model\Parts;


use Attach\Model\FileManager;
use DeltaDb\EntityInterface;
use HttpWarp\Request;
use DeltaUtils\FileSystem;

trait AttachSave
{
    /**
     * @return FileManager
     */
    abstract public function getFileManager();

    /**
     * @return Request
     */
    abstract public function getRequest();

    /**
     * @param EntityInterface $item
     * @param $maxFileSize
     * @param string $type
     * @deprecated
     */
    public function processRequest(EntityInterface $item, $maxFileSize, $type = FileSystem::FST_IMAGE)
    {
        return $this->processFilesRequest($item, $maxFileSize, $type);
    }

    public function processFilesRequest(EntityInterface $item, $maxFileSize, $type = FileSystem::FST_IMAGE)
    {
        $fm = $this->getFileManager();
        //rm files
        $request = $this->getRequest();
        $filesRm = $request->getParam("filesRm", []);
        foreach ($filesRm as $fileId) {
            $fm->deleteById($fileId);
        }

        //save files
        $files = $request->getFiles("files", $type, $maxFileSize);
        $filesTitle = $request->getParam("filesTitle", []);
        $filesDescription = $request->getParam("filesDescription", []);
        foreach ($files as $file) {
            $name = $file->getName();
            $fileFieldName = str_replace(".", "_", $name);
            $title = isset($filesTitle[$fileFieldName]) ? $filesTitle[$fileFieldName] : null;
            $description = isset($filesDescription[$fileFieldName]) ? $filesDescription[$fileFieldName] : null;
            $fm->saveFileForObject($item, $file, $title, $description);
        }
    }

    public function replaceFile(EntityInterface $item, $maxFileSize, $type = FileSystem::FST_IMAGE)
    {
        $fm = $this->getFileManager();
        //rm files
        $request = $this->getRequest();

        //save files
        $files = $request->getFiles("files", $type, $maxFileSize);
        if (empty($files)) {
            return;
        }
        $file = reset($files);
        $title = $request->getParam("fileTitle");
        $description = $request->getParam("fileDescription");
        $fm->saveFileForObject($item, $file, $title, $description);
    }
} 