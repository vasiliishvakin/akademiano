<?php


namespace Akademiano\Attach\Model\Worker;


use Attach\Model\FileEntity;
use Attach\Model\RequestFiles;
use DeltaPhp\Operator\Worker\WorkerMetaMapPropertiesTrait;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\CreateCommand;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Worker\Exception\NotSupportedCommand;
use DeltaPhp\Operator\Worker\WorkerInterface;
use Attach\Model\Command\ParseRequestFilesCommand;

class ParseRequestFilesWorker implements WorkerInterface, DelegatingInterface
{
    use DelegatingTrait;
    use WorkerMetaMapPropertiesTrait;

    public function execute(CommandInterface $command)
    {
        if ($command->getName() !== ParseRequestFilesCommand::COMMAND_PARSE_REQUEST_FILES) {
            throw new NotSupportedCommand($command);
        }
        $request = $command->getParams("request");
        $fieldName = $command->getParams("fieldName", "files");
        $type = $command->getParams("type");
        $maxFileSize = $command->getParams("maxFileSize");

        $fileClass = $command->getClass();

        /**
         * @param $id
         * @param $data
         * @return FileEntity
         */
        $createFile = function ($id, $data = []) use ($fileClass) {
            $command = new CreateCommand($fileClass);
            /** @var FileEntity $file */
            $file = $this->delegate($command);
            if (is_numeric($id)) {
                $file->setId((integer)$id);
            } else {
                $file->setFieldId($id);
            }

            $segregated = isset($data["__segregated__"]) ? $data["__segregated__"] : [];
            foreach ($segregated as $sgField => $sgId) {
                $data[$sgField][$sgId] = true;
            }

            foreach ($data as $paramName => $paramData) {
                if (isset($paramData[$id])) {
                    //change empty strings to null
                    if (is_string($paramData[$id]) && $paramData[$id]==="") {
                        $paramData[$id] = null;
                    }
                    $method = "set" . ucfirst($paramName);
                    if (method_exists($file, $method)) {
                        $file->{$method}($paramData[$id]);
                    }
                    unset($data[$paramName][$id]);
                }
            }
            return $file;
        };

        //save new files
        $filesUploadedRaw = $request->getFiles($fieldName, $type, $maxFileSize);
        $filesUploadedData = $request->getParam("filesData", []);
        $newFiles = new Collection();
        foreach ($filesUploadedRaw as $uploadedFile) {
            $name = $uploadedFile->getName();
            $id = str_replace(".", "_", $name);
            /** @var FileEntity $file */
            $file = $createFile($id, $filesUploadedData);
            $file->setUploadFile($uploadedFile);
            $newFiles[] = $file;
        }

        //update exists
        $filesUpdateRaw = $request->getParam("filesUpdate", []);
        $updatedFiles = new Collection();
        foreach ($filesUpdateRaw as $id) {
            $file = $createFile($id, $filesUploadedData);
            $updatedFiles[] = $file;

        }

        $filesDeletedRaw = $request->getParam("filesRm", []);
        $filesDeleted = new Collection();
        foreach ($filesDeletedRaw as $id) {
            $file = $createFile($id);
            $filesDeleted[] = $file;
        }

        $result = new RequestFiles();
        $result->setUploaded($newFiles);
        $result->setUpdated($updatedFiles);
        $result->setDeleted($filesDeleted);
        return $result;
    }
}
