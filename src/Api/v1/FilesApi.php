<?php

namespace Akademiano\Content\Files\Api\v1;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Content\Files\Model\File;
use Akademiano\Content\Files\Module;
use Akademiano\Config\Config;
use Akademiano\Config\ConfigLoader;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\GenerateIdCommand;
use Akademiano\HttpWarp\File\FileInterface;
use Akademiano\Utils\FileSystem;
use Akademiano\UUID\UuidComplexInterface;
use Akademiano\UUID\UuidComplexShortTables;
use Hashids\Hashids;

class FilesApi extends EntityApi
{
    const API_ID = "filesApi";
    const ENTITY_CLASS = File::class;

    const DEFAULT_CONFIG = [
        Module::MODULE_ID => [
            "filesPath" => [
                "default" => "data/files"
            ],
        ],
    ];

    /** @var  ConfigLoader */
    protected $config;

    /** @var  Hashids */
    protected $hashids;

    protected $rootDir;


    /**
     * @param null $path
     * @param null $default
     * @return Config|mixed|null
     */
    public function getConfig($path = null, $default = null)
    {
        if (null === $this->config) {
            $this->config = new Config(static::DEFAULT_CONFIG);
        }
        return $this->config->get($path, $default);
    }

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

    /**
     * @param ConfigLoader $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function saveUploaded(FileInterface $file, array $attributes = null)
    {
        $fileExt = $file->getExt();
        $tmpPath = $file->getPath();
        $id = $this->generateUuid();

        $newFilePatch = $this->getNewFilePath($fileExt, $id);
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
        $newFile = $this->getOperator()->execute($createCommand);

        if (!empty($attributes)) {
            $this->getOperator()->load($newFile, $attributes);
        }

        $newFile->setId($id);
        $newFile->setPath($path);
        $newFile->setPosition($newFilePatch);
        $this->saveEntity($newFile);

        return $newFile;
    }

    /**
     * @return UuidComplexInterface
     */
    public function generateUuid()
    {
        $idGenerateCommand = new GenerateIdCommand(static::ENTITY_CLASS);
        $id = $this->getOperator()->execute($idGenerateCommand);
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

    public function getNewFilePath($fileExt, UuidComplexInterface $uuid)
    {
        $firstDirsLevelCount = $this->getConfig([Module::MODULE_ID, "firstDirsLevelCount"], 16);
        $secondDirsLevelCount = $this->getConfig([Module::MODULE_ID, "secondDirsLevelCount"], 16);
        $dir1 = ($uuid->getId() + (integer)$uuid->getDate()->format("B")) % $firstDirsLevelCount;
        $dir1 = $this->hash($dir1);
        $dir2 = $uuid->getId() % $secondDirsLevelCount;
        $dir2 = $this->hash($dir2);
        $subDirs = $dir1 . "/" . $dir2;

        if (1 !== strpos($fileExt, ".")) {
            $fileExt = "." . $fileExt;
        }
        $name = "{$subDirs}/{$uuid->getHex()}{$fileExt}";
        return $name;
    }

    public function getSavePath($ext = null, $currentPath = null)
    {
        $configPaths = [];
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

        return $this->getOperator()->delete($entity);
    }
}
