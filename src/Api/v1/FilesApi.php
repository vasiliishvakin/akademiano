<?php

namespace Akademiano\Content\Files\Api\v1;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\Config\ConfigurableTrait;
use Akademiano\Content\Files\Model\File;
use Akademiano\Content\Files\Model\FileFormatCommand;
use Akademiano\Content\Files\Module;
use Akademiano\Config\Config;
use Akademiano\Config\ConfigLoader;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\GenerateIdCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\HttpWarp\File\FileInterface;
use Akademiano\Operator\Exception\NotFoundSuitableWorkerException;
use Akademiano\Utils\FileSystem;
use Akademiano\UUID\UuidComplexInterface;
use Akademiano\UUID\UuidComplexShortTables;
use Hashids\Hashids;

class FilesApi extends EntityApi implements ConfigurableInterface
{
    const API_ID = "filesApi";
    const ENTITY_CLASS = File::class;
    const MODULE_ID = Module::MODULE_ID;
    const IS_PUBLIC = false;

    use ConfigurableTrait;

    /** @var  Hashids */
    protected $hashids;

    protected $rootDir;

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            if (defined("ROOT_DIR")) {
                $this->rootDir = ROOT_DIR;
            } else {
                $rootDir = realpath(__DIR__ . '/../../../../');
                if ($rootDir && is_dir($rootDir . DIRECTORY_SEPARATOR . 'vendor')) {
                    $this->rootDir = $rootDir;
                } else {
                    throw new \RuntimeException("Root dir not defined and not found");
                }
            }
        }
        return $this->rootDir;
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function saveUploaded(FileInterface $file, array $attributes = null)
    {
        $fileExt = $file->getExt();
        $tmpPath = $file->getPath();
        $id = $this->generateUuid();

        $position = $this->getNewPosition($id);
        $newFilePatch = $this->getNewFilePath($position, $fileExt, $id);
        $savedPath = $this->getSavePath($fileExt, $tmpPath);
        $path = $savedPath . DIRECTORY_SEPARATOR . $newFilePatch;
        $fullNewPath = $this->getRootDir() . DIRECTORY_SEPARATOR . $path;
        $dir = dirname($fullNewPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0750, true);
        }
        if (!$file->mv($fullNewPath)) {
            throw new \RuntimeException(sprintf('Could not move file from "%s" to "%s"', $tmpPath, $fullNewPath));
        }

        $createCommand = new CreateCommand(static::ENTITY_CLASS);
        /** @var File $newFile */
        $newFile = $this->delegate($createCommand);

        if (!empty($attributes)) {
            $this->delegate((new LoadCommand($newFile))->setData($attributes));
        }

        $newFile->setId($id);
        $newFile->setPath($path);
        $newFile->setPosition($position);
        $this->saveEntity($newFile);

        return $newFile;
    }

    /**
     * @return UuidComplexInterface
     */
    public function generateUuid()
    {
        $idGenerateCommand = new GenerateIdCommand(static::ENTITY_CLASS);
        $id = $this->delegate($idGenerateCommand);
        return new UuidComplexShortTables($id);
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

    public function getNewPosition(UuidComplexInterface $uuid)
    {
        $firstDirsLevelCount = $this->getConfig([Module::MODULE_ID, "firstDirsLevelCount"], 16);
        $secondDirsLevelCount = $this->getConfig([Module::MODULE_ID, "secondDirsLevelCount"], 16);
        $dir1 = ($uuid->getId() + (integer)$uuid->getDate()->format("B")) % $firstDirsLevelCount;
        $dir1 = $this->hash($dir1);
        $dir2 = $uuid->getId() % $secondDirsLevelCount;
        $dir2 = $this->hash($dir2);
        $subDirs = $dir1 . DIRECTORY_SEPARATOR . $dir2;
        return $subDirs;
    }

    public function getNewFilePath($subDirs, $fileExt, UuidComplexInterface $uuid)
    {
        if (1 !== strpos($fileExt, ".")) {
            $fileExt = "." . $fileExt;
        }
        $name = $subDirs . DIRECTORY_SEPARATOR . $uuid->getHex() . $fileExt;
        return $name;
    }

    public function getSavePath($ext = null, $currentPath = null)
    {
        $configPaths = [];
        if (static::MODULE_ID !== Module::MODULE_ID) {
            $configPaths = [
                [static::MODULE_ID, "filesPath", "default"],
                [static::MODULE_ID, "filesPath"],
            ];
        }
        if ($ext) {
            $configPaths[] = ["filesPath", $ext];
        }
        if ($currentPath) {
            $fileMime = FileSystem::getFileType($currentPath);
            $configPaths[] = [Module::MODULE_ID, "filesPath", $fileMime];
            $fileType = FileSystem::getFileTypeConst($currentPath);
            $configPaths[] = [Module::MODULE_ID, "filesPath", $fileType];
        }
        $configPaths[] = [Module::MODULE_ID, "filesPath", "default"];
        $configPaths[] = [Module::MODULE_ID, "filesPath"];
        $path = $this->getConfig()->getOneIs($configPaths);
        if (is_array($path)) {
            throw new \RuntimeException("Many option for file path available");
        }
        return $path;
    }

    public function deleteEntity(EntityInterface $entity)
    {
        $resource = sprintf('entityapi:delete:%s:%s', static::ENTITY_CLASS, $entity->getId());
        if (!$this->accessCheck($resource, $entity->getOwner())) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        if ($entity instanceof File) {
            $filePath = realpath($this->getRootDir() . DIRECTORY_SEPARATOR . $entity->getPath());
            if ($filePath) {
                unlink($filePath);
            }
        }

        return $this->delegate(new DeleteCommand($entity));
    }

    public function isPublic()
    {
        return static::IS_PUBLIC;
    }

    public function formatFile(File $file, string $extension, string $template)
    {
        $savePath = $this->getSavePath();
        $command = (new FileFormatCommand($file, $savePath, $extension))->setTemplate($template)->setPublic($this->isPublic());
        $formattedFile = $this->delegate($command);
        return $formattedFile;
    }
}
