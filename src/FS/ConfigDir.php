<?php


namespace Akademiano\Config\FS;


use Akademiano\Config\Exception\ConfigDirectoryNotReadException;
use Akademiano\Config\Exception\ConfigFileInvalidTypeException;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\FileSystem;

class ConfigDir
{
    const LEVEL_DEFAULT = 0;

    protected $rawPath;
    protected $path;
    protected $level;

    /** @var ConfigFile[] */
    protected $files = [];

    protected $typePrefixMatrix = [];

    protected $content = [];

    protected $params = [];

    public function __construct($path, $level, array $params = null)
    {
        $this->setPath($path);
        $this->setLevel($level);
        if (null !== $params) {
            $this->setParams($params);
        }

        $this->addType(ConfigFile::TYPE_GLOBAL, "");
        $this->addType(ConfigFile::TYPE_AUTO, "auto");
        $this->addType(ConfigFile::TYPE_LOCAL, "local");
    }

    public function addType($type, $prefix)
    {
        $this->typePrefixMatrix[$type] = $prefix;
    }

    public function getTypePrefix($type)
    {
        if (!isset($this->typePrefixMatrix[$type])) {
            throw  new ConfigFileInvalidTypeException(sprintf('Type "%s" config file not defined', $type));
        }
        return $this->typePrefixMatrix[$type];
    }

    public function getTypes()
    {
        return array_keys($this->typePrefixMatrix);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (null === $this->path) {
            $path = realpath($this->rawPath);
            if (!$path) {
                throw  new ConfigDirectoryNotReadException('Path "%s" path not exist', $this->rawPath);
            }
            $this->path = $path;
        }
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->rawPath = $path;
        $this->path = null;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return array
     */
    public function getParams($path = null, $default = null)
    {
        if (null !== $path) {
            return ArrayTools::get($this->params, $path, $default);
        } else {
            return $this->params;
        }
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function addFile(ConfigFile $file)
    {
        $this->files[$file->getId()] = $file;
    }

    protected function findFile($configName, $type)
    {
        $directory = $this->getPath();
        if (!is_dir($directory)) {
            throw new ConfigDirectoryNotReadException(sprintf('Directory "%s" is not readable (or not exist)', $directory));
        }
        $prefix = $this->getTypePrefix($type);
        if ($prefix !== "") {
            $prefix = $prefix . ".";
        }
        $filePath =  $prefix . $configName . "." . ConfigFile::EXT;
        if ($filePath === FileSystem::sanitize($filePath, $directory)) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $filePath;
        } else {
            return null;
        }
        return $filePath;
    }

    /**
     * @param $configName
     * @return ConfigFile[]|array
     */
    public function getFiles($configName)
    {
        if (!isset($this->files[$configName])) {
            $types = $this->getTypes();
            $files = [];
            foreach ($types as $type) {
                $file = $this->findFile($configName, $type);
                if ($file) {
                    $files[$type] = new ConfigFile($file, $type);
                }
            }
            $this->files[$configName] = $files;
        }
        if (empty($this->files[$configName])) {
            return [];
        }
        return $this->files[$configName];
    }

    /**
     * @param $configName
     * @return array
     */
    protected function read($configName)
    {
        $files = $this->getFiles($configName);
        $content = [];
        foreach ($files as $file) {
            $content = ArrayTools::mergeRecursiveDisabled($content, $file->getContent());
        }
        return $content;
    }

    /**
     * @param $configName
     * @return array
     */
    public function getContent($configName)
    {
        if (!isset($this->content[$configName])) {
            $this->content[$configName] = $this->read($configName);
        }
        return $this->content[$configName];
    }
}
