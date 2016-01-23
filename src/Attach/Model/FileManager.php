<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Attach\Model;


use DeltaCore\Config;
use DeltaCore\Parts\Configurable;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use DeltaUtils\FileSystem;
use DeltaUtils\StringUtils;
use Hashids\Hashids;
use HttpWarp\File\FileInterface;
use HttpWarp\File\UploadFile;
use Sequence\Model\Parts\Sequence;
use Sequence\Model\SequenceManagerInterface;
use UUID\Model\Parts\UuidTrait;
use UUID\Model\UuidComplexShort;
use UUID\Model\UuidFactory;

class FileManager extends Repository
{
    use Configurable;
    use Sequence;

    protected $rootUri;
    /** @var  Hashids */
    protected $hashids;

    /** @var  UuidFactory */
    protected $uuidFactory;

    protected $metaInfo = [
        "fields" => [
            "id",
            "section",
            "object",
            "type",
            "sub_type",
            "name",
            "description",
            "path",
            "uuid",
            "main",
            "order",
            "info",
            "created",
        ]
    ];

    public function getRelationsConfig()
    {
        return $this->getConfig()->get(["Attach", "relationMatrix"], []);
    }

    public function getSectionsConfig()
    {
        return $this->getConfig()->get(["Attach", "sectionMatrix"], []);
    }

    public function getSequenceName()
    {
        $config = $this->getConfig();
        $sequence = $config->get(["Attach", "sequence"], "default");
        return $sequence;
    }

    /**
     * @return mixed
     */
    public function getRootUri()
    {
        if (is_null($this->rootUri)) {
            $this->rootUri = "http://";
            if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || $_SERVER['SERVER_PORT'] == 443
            ) {
                $this->rootUri = "https://";
            }
            $this->rootUri .= $_SERVER["SERVER_NAME"];
        }
        return $this->rootUri;
    }

    /**
     * @param mixed $rootUri
     */
    public function setRootUri($rootUri)
    {
        $this->rootUri = $rootUri;
    }

    public function getSectionHash($entityClass)
    {
        if (is_object($entityClass)) {
            $entityClass = get_class($entityClass);
        }
        $hash = hexdec(hash("crc32", $entityClass));
        if ($hash <= 1000) {
            $hash = $hash + 1000;
        } elseif ($hash > 100000) {
            $hash = ceil($hash / 9999);
        }
        return $hash;
    }

    public function getSection($entityClass)
    {
        $typesConfig = $this->getSectionsConfig();
        if (is_object($entityClass)) {
            $entityClass = get_class($entityClass);
        }
        $section = null;
        if ($typesConfig instanceof Config) {
            $section = $typesConfig->get($entityClass);
        }
        if (null === $section) {
            $section = $this->getSectionHash($entityClass);
        }
        return $section;
    }

    /**
     * @return UuidFactory
     */
    public function getUuidFactory()
    {
        return $this->uuidFactory;
    }

    /**
     * @param UuidFactory $uuidFactory
     */
    public function setUuidFactory($uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @return mixed
     */
    public function getLastChanged()
    {
        return $this->lastChanged;
    }

    /**
     * @param mixed $lastChanged
     */
    public function setLastChanged($lastChanged)
    {
        $this->lastChanged = $lastChanged;
    }

    public function createUuid()
    {
        $sm = $this->getSequenceManager();
        $adapter = $sm->getAdapter("PgSequenceUuidComplexShort");
        $uuid = $adapter->getNext();
        $uf = $this->getUuidFactory();
        $uuid = $uf->create($uuid);
        return $uuid;
    }

    /**
     * @deprecated
     */
    public function getNextSequence()
    {
        $sequence = $this->getSequenceName();
        $sm = $this->getSequenceManager();
        $next = $sm->getNext($sequence);
        return $next;
    }

    public function getSavePath($ext = null, $currentPath = null)
    {
        $configPaths = [];
        if ($ext) {
            $configPaths[] = ["Attach", "filesPath", $ext];
        }
        if ($currentPath) {
            $fileMime = FileSystem::getFileType($currentPath);
            $configPaths[] = ["Attach", "filesPath", $fileMime];
            $fileType = FileSystem::getFileTypeConst($currentPath);
            $configPaths[] = ["Attach", "filesPath", $fileType];
        }
        $configPaths[] = ["Attach", "filesPath", "default"];
        $configPaths[] = ["Attach", "filesPath"];

        $path = $this->getConfig()->getOneIs($configPaths);
        if (is_array($path)) {
            throw new \RuntimeException("Many option for file path available");
        }
        return $path;
    }

    /**
     * @return Hashids
     */
    public function getHahids()
    {
        if (null === $this->hashids) {
            $salt = $this->getConfig(["Attach", "hashids", "salt"], __FILE__);
            $this->hashids = new Hashids($salt, 4, "qwertyuiopasdfghjklzxcvbnm123456789");
        }
        return $this->hashids;
    }

    public function hash($value)
    {
        return $this->getHahids()->encode($value);
    }

    public function getNewFilePath($ext = null, $currentPath = null, UuidComplexShort $uuid)
    {
        $firstDirsLevelCount = $this->getConfig(["Attach", "firstDirsLevelCount"], 16);
        $secondDirsLevelCount = $this->getConfig(["Attach", "secondDirsLevelCount"], 16);

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

    public function saveFileIO(FileInterface $file, UuidComplexShort $uuid = null)
    {
        $fileExt = $file->getExt();
        $tmpPath = $file->getPath();
        if (null === $uuid) {
            $uuid = $this->createUuid();
        }
        $newFile = $this->getNewFilePath($fileExt, $tmpPath, $uuid);
        $fullNewPath = ROOT_DIR . "/" . $newFile;
        $dir = dirname($fullNewPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0750, true);
        }
        if (!$file->mv($fullNewPath)) {
            return false;
        };
        return $newFile;
    }

    public function create(array $data = null)
    {
        /** @var File $entity */
        $entity = parent::create($data);
        $entity->setRootUri($this->getRootUri());
        $entity->setUuidFactory($this->getUuidFactory());
        return $entity;
    }

    public function filterFileData(array $data)
    {
        $notEmptyFunction = function ($value) {
            $value = trim($value);
            if (empty($value)) {
                return null;
            }
            return $value;
        };

        $filters = [
            'main' => [
                'filter' => FILTER_VALIDATE_BOOLEAN,
                'flags' => FILTER_NULL_ON_FAILURE,
            ],
            'title' => [
                'filter' => FILTER_CALLBACK,
                'options' => $notEmptyFunction,
                'flags' => FILTER_NULL_ON_FAILURE,
            ],
            'description' => [
                "filter" => FILTER_CALLBACK,
                'options' => $notEmptyFunction,
                'flags' => FILTER_NULL_ON_FAILURE,
            ],
            'order' => [
                'filter' => FILTER_VALIDATE_INT,
                'flags' => FILTER_NULL_ON_FAILURE,
            ],
            'info' => [
                'filter' => FILTER_CALLBACK,
                'options' => function ($value) {
                    if (empty($value)) {
                        return null;
                    } elseif (StringUtils::isJson($value)) {
                        return $value;
                    }
                    return null;
                },
                'flags' => FILTER_NULL_ON_FAILURE,
            ],
        ];
        $data = filter_var_array($data, $filters);

        $fileInfo = [];

        if (!empty($data["title"])) {
            $fileInfo["name"] = $data["title"];
        }
        if (!empty($data["description"])) {
            $fileInfo["description"] = $data["description"];
        }
        if (!empty($data["order"])) {
            $fileInfo["order"] = (integer)$data["order"];
        }
        if (!empty($data["main"])) {
            $fileInfo["main"] = (bool)$data["main"];
        }
        if (!empty($data["info"])) {
            $fileInfo["info"] = $data["info"];
        }

        return $fileInfo;
    }

    public function saveFileForObject(EntityInterface $object, FileInterface $file, $data = [])
    {
        $section = $this->getSection($object);
        $objId = $object->getId();
        $uuid = $this->createUuid();
        $path = $this->saveFileIO($file, $uuid);
        if (!$path) {
            throw new \RuntimeException("file not saved");
        }

        $fileInfo = $this->filterFileData($data);

        $fileInfo["section"] = $section;
        $fileInfo["object"] = $objId;
        $fileInfo["path"] = $path;
        $fileInfo["type"] = $file->getType();
        $fileInfo["sub_type"] = $file->getSubType();
        $fileInfo["uuid"] = $uuid;

        $file = $this->create($fileInfo);
        $this->save($file);
    }

    public function getFilesForObject(EntityInterface $object, $criteria = [])
    {
        $section = $this->getSection($object);
        $id = $object->getId();
        $criteria ["section"] = $section;
        $criteria ["object"] = $id;
        $items = $this->find($criteria, null, null, null, ["order" => "asc"]);
        return $items;
    }

    /**
     * @param integer|string|array|EntityInterface|EntityInterface[] $objectsIds
     * @param null|string $entityClass
     * @param array $criteria
     */
    public function deleteFilesForObjects($objectsIds, $entityClass = null, $criteria = [])
    {
        $objectsIds = (array)$objectsIds;
        $ids = [];
        foreach ($objectsIds as $object) {
            if ($object instanceof EntityInterface) {
                $ids[get_class($object)][] = $object->getId();
            } else {
                if (null === $entityClass) {
                    throw new \LogicException("Need set entityClass forwork with array of ids");
                }
                $ids[$entityClass][] = $object;
            }
        }
        if (empty($ids)) {
            return;
        }
        foreach ($ids as $class => $idsForClass) {
            if (empty($idsForClass)) {
                continue;
            }
            $section = $this->getSection($class);
            $criteria ["section"] = $section;
            $criteria ["object"] = $idsForClass;
            $this->deleteRaw($criteria);
        }
    }

    public function deleteRaw(array $criteria = [], $table = null)
    {
        $items = $this->findRaw($criteria, $table);
        $deletedRows = parent::deleteRaw($criteria, $table);
        foreach ($items as $item) {
            if (isset($item["path"])) {
                $path = ROOT_DIR . DIRECTORY_SEPARATOR . $item["path"];
                unlink($path);
            }
        }
        return $deletedRows;
    }

    public function updateFile($id, array $data = [])
    {
        $file = $this->findById($id);
        if (!$file) {
            return;
        }
        $data = $this->filterFileData($data);
        $this->load($file, $data);
        $result = $this->save($file);
        return $result;
    }
}
