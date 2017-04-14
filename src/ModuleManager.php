<?php

namespace Akademiano\Core;


use Composer\Autoload\ClassLoader;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\Parts\InnerCache;
use Akademiano\Router\Route;
use Pimple\Container;
use Akademiano\Config\Config;

class ModuleManager
{
    use InnerCache;

    /**
     * @var array
     */
    protected $modulesList;

    /**
     * @var ClassLoader
     */
    protected $loader;

    /** @var  Container */
    protected $diContainer;

    /** @var  array */
    protected $configDirs;

    public function __construct(array $modules, Container $diContainer)
    {
        $this->setModulesList($modules);
        $this->setDiContainer($diContainer);
    }

    /**
     * @param array $modulesList
     */
    public function setModulesList(array $modulesList)
    {
        $this->modulesList = $modulesList;
    }

    /**
     * @return Container
     */
    public function getDiContainer()
    {
        return $this->diContainer;
    }

    /**
     * @param Container $diContainer
     */
    public function setDiContainer(Container $diContainer)
    {
        $this->diContainer = $diContainer;
    }

    /**
     * @return array
     */
    public function getModulesList()
    {
        return $this->modulesList;
    }

    public function addModule($name)
    {
        $this->modulesList[] = $name;
    }

    /**
     * @param ClassLoader $loader
     */
    public function setLoader(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    public function load(Application $application = null)
    {
        $modules = $this->getModulesList();
        foreach ($modules as $moduleName) {
            $class = "\\{$moduleName}\\Module";
            if ($class instanceof ModuleInterface) {
                $module = new $class;
                $module->init($this, $application);
            }
        }
    }

    public function getModulePath($moduleName)
    {
        $cacheKey = "ModulePath|{$moduleName}";
        $path = $this->getInnerCache($cacheKey);
        if ($path) {
            return $path;
        }
        $class = "{$moduleName}\\Module";
        $loader = $this->getLoader();
        $file = $loader->findFile($class);
        $path = null;
        if (!$file) {
            $moduleDir = ROOT_DIR . "/modules/{$moduleName}";
            if (!file_exists($moduleDir)) {
                throw new \Exception("module $moduleName not found");
            }
            $path = realpath($moduleDir);
            $loader->add($moduleName, ROOT_DIR . "/modules");
            $loader->addClassMap(["{$moduleName}\\Module" => $path . "/Module.php"]);
        } else {
            $path = dirname($file);
        }
        $this->setInnerCache($cacheKey, $path);
        if (!$path) {
            return null;
        }

        return $path;
    }

    public function getModuleConfigPath($moduleName)
    {
        $path = $this->getModulePath($moduleName);
        if (!$path) {
            return null;
        }
        $path = $path . "/config";
        if (!file_exists($path)) {
            return null;
        }
        return $path;
    }

    public function getConfigDirs()
    {
        if (null === $this->configDirs) {
            $modules = $this->getModulesList();
            $this->configDirs = [];
            foreach ($modules as $module) {
                $dir = $this->getModuleConfigPath($module);
                if ($dir) {
                    $this->configDirs[] = $dir;
                }
            }
        }
        return $this->configDirs;
    }
}
