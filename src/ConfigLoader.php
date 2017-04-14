<?php

namespace Akademiano\Config;


use Akademiano\Config\FS\ConfigDir;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Exception\PathRestrictException;
use Akademiano\Utils\FileSystem;
use Akademiano\Utils\Parts\DIContainerTrait;

class ConfigLoader implements DIContainerIncludeInterface
{
    use DIContainerTrait;

    const NAME_CONFIG = "config";

    protected $rootDir;

    protected $paths = [];

    protected $configDirs = [];

    /** @var Config[] */
    protected $config = [];

    public function setConfigDirs(array $paths, $level = null)
    {
        if (isset($this->configDirs[$level])) {
            $this->configDirs[$level] = [];
        }
        foreach ($paths as $path) {
            $this->addConfigDir($path, $level);
        }
    }

    public function addConfigDir($path, $level = ConfigDir::LEVEL_DEFAULT)
    {
        $this->paths[$level][$path] = $path;
    }

    public function getLevels()
    {
        return array_keys($this->paths);
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getRootDir()
    {
        if (null === $this->rootDir) {
            if (defined("ROOT_DIR")) {
                $this->rootDir = ROOT_DIR;
            }
        }
        return $this->rootDir;
    }

    /**
     * @param $level
     * @return ConfigDir[]
     */
    public function getConfigDirs($level)
    {
        if (!isset($this->configDirs[$level])) {
            $this->configDirs[$level] = [];
        }
        if (!empty($this->paths[$level])) {
            foreach ($this->paths[$level] as $path) {
                if (is_dir($path) && is_readable($path)) {
                    if (!FileSystem::inDir($this->getRootDir(), $path)) {
                        throw new PathRestrictException('Path %s not in Root Path', $path);
                    }
                    $this->configDirs[$level][$path] = new ConfigDir($path, $level);
                }
            }
            unset($this->paths[$level]);
        }
        return $this->configDirs[$level];
    }

    protected function read($level, $name = self::NAME_CONFIG)
    {
        $dirs = $this->getConfigDirs($level);
        $config = [];
        foreach ($dirs as $dir) {
            $config = ArrayTools::mergeRecursiveDisabled($config, $dir->getContent($name));
        }
        return $config;
    }

    public function getConfig($name = self::NAME_CONFIG)
    {
        if (!isset($this->config[$name])) {
            $levels = $this->getLevels();
            $config = [];
            foreach ($levels as $level) {
                $config = ArrayTools::mergeRecursiveDisabled($config, $this->read($level, $name));
            }
            $this->config[$name] = new Config($config, $this->getDiContainer());
        }
        return $this->config[$name];
    }
}
