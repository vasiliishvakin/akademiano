<?php

namespace Akademiano\Attach\Model\Parts;


use Attach\Model\FileManager;
use DeltaDb\EntityInterface;
use HttpWarp\Request;
use DeltaUtils\FileSystem;

/**
 * @deprecated
 */
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
        $filesData = $request->getParam("filesData", []);


        $fileDataFunction = function ($id, &$data) {
            $fileData = [];
            foreach($data as $paramName => $paramData) {
                if (is_numeric($id) &&  isset($paramData[(integer)$id])) {
                    $id = (integer) $id;
                }

                if (isset($paramData[$id])) {
                    $fileData[$paramName] = $paramData[$id];
                    unset($data[$paramName][$id]);
                }
                if ($paramName === "main" && (string)$paramData === (string)$id) {
                    $fileData["main"] = true;
                }
            }
            return $fileData;
        };

        //save new files
        foreach ($files as $file) {
            $name = $file->getName();
            $id = str_replace(".", "_", $name);
            $fileData = $fileDataFunction($id, $filesData);
            $fm->saveFileForObject($item, $file, $fileData);
        }
        //update exists
        $filesUpdate = $request->getParam("filesUpdate", []);
        foreach($filesUpdate as $id) {
            $fileData = $fileDataFunction($id, $filesData);
            $fm->updateFile($id, $fileData);
        }

    }

    public function replaceFile(EntityInterface $item, $maxFileSize, $type = FileSystem::FST_IMAGE)
    {
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

        $fm = $this->getFileManager();
        $filesRm = $fm->getFilesForObject($item);
        foreach ($filesRm as $fileRm) {
            $fm->deleteById($fileRm->getId());
        }
        $fm->saveFileForObject($item, $file, $title, $description);
    }

}
