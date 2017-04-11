<?php

namespace Akademiano\Config;


use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\FileSystem;
use Akademiano\Utils\Parts\DIContainerTrait;
use Pimple\Container;

class ConfigLoader implements DIContainerIncludeInterface
{
    use DIContainerTrait;

    const LOCAL_CONFIG = 'local';
    const GLOBAL_CONFIG = 'global';
    const AUTO_CONFIG = 'auto';
    const LEVEL_APP = "App";
    const LEVEL_PROJECT = "Project";
    const LEVEL_SITE = "Site";
    const LEVELS = [self::LEVEL_APP, self::LEVEL_PROJECT, self::LEVEL_SITE];
    const TYPES_CONFIG = [self::GLOBAL_CONFIG, self::AUTO_CONFIG, self::LOCAL_CONFIG];

    const NAME_CONFIG = "config";
    const NAME_RESOURCES = "resources";
    const NAME_ROUTERS = "routers";
    const FORMAT_PHP = "php";

    const CONFIG_DIR = "config";

    protected $configDirs;
    protected $configObj;

    /** @var  Container */
    protected $diContainer;

    public function __construct(Container $diContainer, array $configDirs = null)
    {
        $this->setDiContainer($diContainer);
        if (!empty($configDirs)) {
            $this->setConfigDirs($configDirs);
        }
    }

    /**
     * @param mixed $configDirs
     */
    public function setConfigDirs($configDirs)
    {
        $this->configDirs = $configDirs;
    }

    public function getConfigDirs()
    {
        if (null === $this->configDirs) {
            $configDirs = [];
            $diContainer = $this->getDiContainer();

            if (empty($configDirs)) {
                if (isset($diContainer["appDir"])) {
                    $appDir = $diContainer["appDir"];
                    if (null !== $appDir) {
                        $configDirs[self::LEVEL_APP] = $appDir . DIRECTORY_SEPARATOR . self::CONFIG_DIR;
                    }
                }

                if (isset($diContainer["sharedSiteDir"])) {
                    $projectConfigDir = $diContainer["sharedSiteDir"];
                    if (null !== $projectConfigDir) {
                        $dir = $projectConfigDir . DIRECTORY_SEPARATOR . "config";
                        if (is_dir($dir)) {
                            $configDirs[self::LEVEL_PROJECT] = $projectConfigDir . DIRECTORY_SEPARATOR . "config";
                        }
                    }
                }

                if (isset($diContainer["currentSiteDir"])) {
                    $siteConfigDir = $diContainer["currentSiteDir"];
                    if (null !== $siteConfigDir) {
                        $dir = $siteConfigDir . DIRECTORY_SEPARATOR . "config";
                        if (is_dir($dir)) {
                            $configDirs[self::LEVEL_SITE] = $dir;
                        }
                    }
                }
            }
            $this->configDirs = $configDirs;
        }
        return $this->configDirs;
    }

    public function getConfigDir($level)
    {
        if (isset($this->getConfigDirs()[$level])) {
            return $this->configDirs[$level];
        }
    }

    public function readConfig($level, $type, $name = self::NAME_CONFIG, $format = self::FORMAT_PHP, $default = [])
    {
        switch ($type) {
            case self::GLOBAL_CONFIG:
                $prefix = "";
                break;
            case self::LOCAL_CONFIG:
                $prefix = "local.";
                break;
            case self::AUTO_CONFIG:
                $prefix = "auto.";
                break;
        }
        $file = $this->getConfigDir($level) . "/{$prefix}{$name}.{$format}";

        switch ($format) {
            case self::FORMAT_PHP:
                return FileSystem::getPhpConfig($file, $default);
                break;
            default:
                throw new \InvalidArgumentException("Reader for format {$format} not found");
        }
    }

    /**
     * @param array $config
     * @deprecated
     */
    public function joinConfigLeft(array $config)
    {
        $confObj = $this->getConfig();
        $confObj->joinLeft($config);
    }

    /**
     * @param array $config
     * @deprecated
     */
    public function joinConfigRight(array $config)
    {
        $confObj = $this->getConfig();
        $confObj->joinRight($config);
    }


    /**
     * @param string $name
     * @return Config
     */
    public function getConfig($name = self::NAME_CONFIG)
    {
        $fullConfig = [];
        foreach (self::LEVELS as $level) {
            foreach (self::TYPES_CONFIG as $type) {
                $config = $this->readConfig($level, $type, $name, self::FORMAT_PHP, null);
                if (null !== $config) {
                    $fullConfig = ArrayTools::mergeRecursiveDisabled($fullConfig, $config);
                }
            }
        }
        return new Config($fullConfig, $this->getDiContainer());
    }
}
