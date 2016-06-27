<?php


namespace Attach\Model\Worker;


use Attach\Model\Command\AddFileCommand;
use Attach\Model\Command\EntityAttachSaveCommand;
use Attach\Model\Command\ParseRequestFilesCommand;
use Attach\Model\FileEntity;
use Attach\Model\RequestFiles;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\CreateCommand;
use DeltaPhp\Operator\Command\DeleteCommand;
use DeltaPhp\Operator\Command\FindCommand;
use DeltaPhp\Operator\Command\GenerateIdCommand;
use DeltaPhp\Operator\Command\GetCommand;
use DeltaPhp\Operator\Command\RelationParamsCommand;
use DeltaPhp\Operator\Command\SaveCommand;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Entity\RelationEntity;
use DeltaPhp\Operator\Worker\ConfigurableInterface;
use DeltaPhp\Operator\Worker\ConfigurableTrait;
use DeltaPhp\Operator\Worker\Exception\NotSupportedCommand;
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use Hashids\Hashids;
use DeltaUtils\FileSystem;
use HttpWarp\File\FileInterface;
use UUID\Model\UuidComplexInterface;
use Attach\Model\Command\UpdateFileCommand;
use Attach\Model\Command\DeleteFileCommand;

class EntityAttachSaveWorker implements WorkerInterface, DelegatingInterface, ConfigurableInterface
{
    use DelegatingTrait;
    use ConfigurableTrait;

    /** @var  Hashids */
    protected $hashids;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case EntityAttachSaveCommand::COMMAND_ATTACH_SAVE: {
                return $this->attachSave($command);
            }
            case AddFileCommand::COMMAND_ADD_FILE:
                return $this->addFile($command->getParams("file"), $command->getParams("entity"), $command->getClass());
            case UpdateFileCommand::COMMAND_UPDATE_FILE:
                return $this->updateFile($command->getParams("file"));
            case DeleteFileCommand::COMMAND_DELETE_FILE:
                return $this->deleteFile($command->getParams("file"), $command->getParams("entity"), $command->getClass());
            default :
                throw new NotSupportedCommand($command);
        }

    }

    public function attachSave(EntityAttachSaveCommand $command)
    {
        $relationClass = $command->getClass();
        /** @var EntityInterface $entity */
        $entity = $command->getParams("entity");

        $relationParamCommand = new RelationParamsCommand("anotherClass", $relationClass, ["class" => get_class($entity)]);
        $fileClass = $this->delegate($relationParamCommand);
        $fileTypes = $command->getParams("fileTypes", [FileSystem::FST_IMAGE]);

        $commandProcessRequestFiles = new ParseRequestFilesCommand($command->getParams("request"), $fileClass, $fileTypes,
            [
                "maxFileSize" => $command->getParams("maxFileSize"),
            ]);
        /** @var RequestFiles $requestFiles */
        $requestFiles = $this->delegate($commandProcessRequestFiles);

        foreach ($requestFiles->getUploaded() as $file) {
            $this->addFile($file, $entity, $relationClass);
        }
        foreach ($requestFiles->getUpdated() as $file) {
            $this->updateFile($file);
        }
        foreach ($requestFiles->getDeleted() as $file) {
            $this->deleteFile($entity, $file, $relationClass);
        }
    }


    public function addFile(FileEntity $file, EntityInterface $entity, $relationClass)
    {
        $getId = function ($class) {
            if (is_object($class)) {
                $class = get_class($class);
            }
            $idGenerateCommand = new GenerateIdCommand($class);
            $id = $this->delegate($idGenerateCommand);
            return $id;
        };


        $fileId = $getId($file);
        $file->setId($fileId);
        //move file
        $path = $this->saveFileIO($file->getUploadFile(), $fileId);
        $file->setPath($path);

        $saveFileCommand = new SaveCommand($file);
        $this->delegate($saveFileCommand);

        $createRelationCommand = new CreateCommand($relationClass);
        /** @var RelationEntity $relation */
        $relation = $this->delegate($createRelationCommand);
        $relation->setId($getId($relationClass));
        $relation->setFirst($entity);
        $relation->setSecond($file);

        $saveRelationCommand = new SaveCommand($relation);
        $this->delegate($saveRelationCommand);
    }

    public function updateFile(FileEntity $file)
    {
        $getFileCommand = new GetCommand($file->getId(), get_class($file));
        $existFile = $this->delegate($getFileCommand);
        $existFile = $this->mergeFile($existFile, $file);
        $saveCommand = new SaveCommand($existFile);
        $this->delegate($saveCommand);
    }

    public function deleteFile(EntityInterface $entity, FileEntity $file, $relationClass)
    {
        $relationsCommand = new FindCommand($relationClass, ["first"=>$entity->getId(),"second" => $file->getId()]);
        $relations = $this->delegate($relationsCommand);
        foreach ($relations as $relation) {
            $deleteRelationCommand = new DeleteCommand($relation);
            $this->delegate($deleteRelationCommand);
        }

        //delete unlinked files
        $relationsCommand = new FindCommand($relationClass, ["second" => $file->getId()]);
        /** @var Collection $relations */
        $relations = $this->delegate($relationsCommand);
        if ($relations->isEmpty()) {
            $deleteFileCommand = new DeleteCommand($file);
            $this->delegate($deleteFileCommand);
        }
    }

    public function mergeFile(FileEntity $aEntity, FileEntity $bEntity)
    {
        $mergeFields = ["title", "description"];
        foreach ($mergeFields as $field) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            if (method_exists($aEntity, $methodSet) && method_exists($bEntity, $methodGet)) {
                $aEntity->{$methodSet}($bEntity->{$methodGet}());
            }
        }
        return $aEntity;
    }

    /**
     * @return Hashids
     */
    public function getHahids()
    {
        if (null === $this->hashids) {
            $salt = $this->getConfig(["hashids", "salt"], __FILE__);
            $this->hashids = new Hashids($salt, 4, "qwertyuiopasdfghjklzxcvbnm123456789");
        }
        return $this->hashids;
    }

    public function hash($value)
    {
        return $this->getHahids()->encode($value);
    }

    public function getSavePath($ext = null, $currentPath = null)
    {
        $configPaths = [];
        if ($ext) {
            $configPaths[] = ["filesPath", $ext];
        }
        if ($currentPath) {
            $fileMime = FileSystem::getFileType($currentPath);
            $configPaths[] = ["filesPath", $fileMime];
            $fileType = FileSystem::getFileTypeConst($currentPath);
            $configPaths[] = ["filesPath", $fileType];
        }
        $configPaths[] = ["filesPath", "default"];
        $configPaths[] = ["filesPath"];

        $path = $this->getConfig()->getOneIs($configPaths);
        if (is_array($path)) {
            throw new \RuntimeException("Many option for file path available");
        }
        return $path;
    }

    public function getRootDir()
    {
        return ROOT_DIR;
    }

    public function getNewFilePath($ext = null, $currentPath = null, UuidComplexInterface $uuid)
    {
        $firstDirsLevelCount = $this->getConfig(["firstDirsLevelCount"], 16);
        $secondDirsLevelCount = $this->getConfig(["secondDirsLevelCount"], 16);

        $dir1 = ($uuid->getId() + $uuid->getDate()->format("B")) % $firstDirsLevelCount;
        $dir1 = $this->hash($dir1);
        $dir2 = $uuid->getId() % $secondDirsLevelCount;
        $dir2 = $this->hash($dir2);
        $subdirs = $dir1 . "/" . $dir2;
        $savedPath = $this->getSavePath($ext, $currentPath);
        if ($ext) {
            $ext = "." . $ext;
        }
        $name = "{$savedPath}/{$subdirs}/{$uuid->toHex()}{$ext}";
        return $name;
    }

    public function saveFileIO(FileInterface $file, UuidComplexInterface $uuid)
    {
        $fileExt = $file->getExt();
        $tmpPath = $file->getPath();
        $newFile = $this->getNewFilePath($fileExt, $tmpPath, $uuid);
        $fullNewPath = $this->getRootDir() . DIRECTORY_SEPARATOR . $newFile;
        $dir = dirname($fullNewPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0750, true);
        }
        if (!$file->mv($fullNewPath)) {
            return false;
        };
        return $newFile;
    }

}
