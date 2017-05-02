<?php

namespace Akademiano\Config;


use Akademiano\Config\FS\ConfigDir;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Exception\PathRestrictException;
use Akademiano\Utils\FileSystem;
use Akademiano\Utils\Parts\DIContainerTrait;
use Pimple\Container;

class ConfigLoader implements DIContainerIncludeInterface
{
    use DIContainerTrait;

    const NAME_CONFIG = "config";
    const POST_PROCESS_ALL = "__all__";

    protected $rootDir;

    protected $paths = [];

    protected $params = [];

    protected $configDirs = [];

    /** @var Config[] */
    protected $config = [];

    protected $levels = [];

    /** @var \Callable[] */
    protected $postProcessors = [];


    public function __construct(Container $diContainer = null)
    {
        if (null !== $diContainer) {
            $this->setDiContainer($diContainer);
        }
    }


    public function setConfigDirs(array $paths, $level = null)
    {
        $this->levels = [];
        $this->paths = [];
        $this->configDirs = [];
        $this->paths = [];
        foreach ($paths as $path) {
            $this->addConfigDir($path, $level);
        }
    }

    public function addConfigDir($path, $level = ConfigDir::LEVEL_DEFAULT, array $params = null)
    {
        $this->paths[$level][$path] = $path;
        $this->levels[$level] = $level;
        $this->params[$level][$path] = $params;
        $this->config = [];
    }

    public function attachConfigDir(ConfigDir $dir, $level = null)
    {
        if (null !== $level) {
            $dir->setLevel($level);
        }
        $this->configDirs[$dir->getLevel()][$dir->getPath()] = $dir;
        $this->levels[$level] = $level;
    }

    public function getLevels()
    {
        ksort($this->levels);
        return $this->levels;
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
                if (isset($this->params[$level][$path])) {
                    $params = $this->params[$level][$path];
                    unset($this->params[$level][$path]);
                } else {
                    $params = [];
                }
                if (is_dir($path) && is_readable($path)) {
                    if (!FileSystem::inDir($this->getRootDir(), $path)) {
                        throw new PathRestrictException('Path %s not in Root Path', $path);
                    }
                    $this->configDirs[$level][$path] = new ConfigDir($path, $level, $params);
                }
            }
            unset($this->paths[$level]);
        }
        return $this->configDirs[$level];
    }


    public function getPostProcessors($name)
    {
        $processors = isset($this->postProcessors[$name]) ? $this->postProcessors[$name] : [];
        $allProcessors = isset($this->postProcessors[self::POST_PROCESS_ALL]) ? $this->postProcessors[self::POST_PROCESS_ALL] : [];
        $processors = array_merge($processors, $allProcessors);
        return $processors;
    }

    public function addPostProcessor(Callable $function, $name = self::POST_PROCESS_ALL)
    {
        $this->postProcessors[$name][] = $function;
    }

    public function postProcess($dirContent, $dir, $name, $level)
    {
        $content = $dirContent;
        $processors = $this->getPostProcessors($name);
        foreach ($processors as $processor) {
            $content = call_user_func($processor, $content, $dir, $name, $level);
        }
        return $content;
    }

    /**
     * @param $level
     * @param string $name
     * @return array
     */
    protected function read($level, $name = self::NAME_CONFIG)
    {
        $dirs = $this->getConfigDirs($level);
        $config = [];
        foreach ($dirs as $dir) {
            $dirContent = $dir->getContent($name);
            $dirContent = $this->postProcess($dirContent, $dir, $name, $level);
            $config = ArrayTools::mergeRecursiveDisabled($config, $dirContent);
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
