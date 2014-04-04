<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Attach\Model;


use DeltaCore\Config;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use HttpWarp\File\FileInterface;
use HttpWarp\File\UploadFile;
use Sequence\Model\SequenceManagerInterface;

class FileManager extends Repository
{
    /**
     * @var Config
     */
    protected $config;

    /** @var  SequenceManagerInterface */
    protected $sequenceManager;

    protected $metaInfo = [
        "files" => [
            "class"  => "\\Attach\\Model\\File",
            "id"     => "id",
            "fields" => [
                "id",
                "section",
                "object",
                "type",
                "name",
                "description",
                "path",
            ]
        ],
    ];

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Sequence\Model\SequenceManagerInterface $sequenceManager
     */
    public function setSequenceManager(SequenceManagerInterface $sequenceManager)
    {
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * @return \Sequence\Model\SequenceManagerInterface
     */
    public function getSequenceManager()
    {
        return $this->sequenceManager;
    }

    public function getRelationsConfig()
    {
        return $this->getConfig()->get(["Attach","relationMatrix"]);
    }

    public function getSectionsConfig()
    {
        return $this->getConfig()->get(["Attach","sectionMatrix"]);
    }

    public function getSequenceName()
    {
        $config = $this->getConfig();
        $sequence = $config->get(["Attach", "sequence"]);
        return $sequence;
    }

    public function getSection($entityClass)
    {
        $typesConfig = $this->getSectionsConfig();
        if (is_object($entityClass)) {
            $entityClass = get_class($entityClass);
        }
        return $typesConfig->get($entityClass);
    }

    public function getNextSequence()
    {
        $sequence = $this->getSequenceName();
        $sm = $this->getSequenceManager();
        $next = $sm->getNext($sequence);
        return $next;
    }

    public function getSavePath()
    {
        return $this->getConfig()->get(["Attach", "filesPath"]);
    }

    //TODO realise with brain use
    public function getNewFilePath($ext = 'xxx')
    {
        $sequence = $this->getNextSequence();
        $name = str_pad($sequence, 9, "0", STR_PAD_LEFT);
        $dir2 = substr($name, 0, 3);
        $dir1 = substr($name, 3, 3);
        $subdirs = $dir1 . "/" . $dir2;
        $savedPath = $this->getSavePath();
        $name = "{$savedPath}/{$subdirs}/{$name}.{$ext}";
        return $name;
    }

    public function saveFileIO(UploadFile $file)
    {
        $fileExt = $file->getExt();
        $newFile = $this->getNewFilePath($fileExt);
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

    public function saveFileForObject(EntityInterface $object, FileInterface $file, $name = null, $description = null)
    {
        $section = $this->getSection($object);
        $objId = $object->getId();
        $path = $this->saveFileIO($file);
        if (!$path) {
            throw new \RuntimeException("file not saved");
        }
        $fileInfo = [
            "section" => $section,
            "object" => $objId,
            "path" => $path,
        ];
        if (!is_null($name)) {
            $fileInfo["name"] = $name;
        }
        if (!is_null($description)) {
            $fileInfo["description"] = $description;
        }
        $file = $this->create($fileInfo);
        $this->save($file);
    }

    public function getFilesForObject(EntityInterface $object)
    {
        $section = $this->getSection($object);
        $id = $object->getId();
        $criteria = [
            "section" => $section,
            "object" => $id,
        ];
        $items = $this->find($criteria);
        return $items;
    }

} 