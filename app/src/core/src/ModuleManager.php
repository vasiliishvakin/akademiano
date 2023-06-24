<?php

namespace Akademiano\Core;


use Akademiano\Config\FS\ConfigDir;
use Akademiano\Router\Router;
use Composer\Autoload\ClassLoader;
use Akademiano\Utils\Parts\InnerCache;
use Akademiano\Router\Route;
use Akademiano\Utils\ArrayTools;
use Pimple\Container;

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

    public function load(Container $container = null)
    {
        $modules = $this->getModulesList();
        if (null === $container) {
            $container = $this->getDiContainer();
        }
        foreach ($modules as $moduleName) {
            $class = "\\{$moduleName}\\Module";
            $module = new $class();
            if ($module instanceof ModuleInterface) {
                $module->init($this, $container);
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
                $dirPath = $this->getModuleConfigPath($module);
                if ($dirPath) {
                    $this->configDirs[] = [
                        "path" => $dirPath,
                        "params" => [
                            "module" => $module,
                        ]
                    ];
                }
            }
        }
        return $this->configDirs;
    }

    public function getConfigLoaderPostProcessors()
    {
        return [
            Router::CONFIG_NAME => [
                function (array $content, ConfigDir $dir, $name, $level) {
                    if ($name !== Router::CONFIG_NAME) {
                        return $content;
                    }
                    $module = $dir->getParams("module");
                    if (!$module) {
                        return $content;
                    }
                    foreach ($content as $routeId => $route) {
                        $route = Route::normalize($route);
                        if (is_array($route["action"])) {
                            $route["action"] = [
                                [
                                    "module" => $module,
                                    "controller" => $route["action"][0]
                                ],
                                "action" => $route["action"][1],
                            ];
                        }
                        $content[$routeId] = $route;
                    }
                    return $content;
                },
            ]
        ];
    }

    public function getListArrayConfigs($fileConfigName, $recursiveMerge = false)
    {
        $modules = $this->getModulesList();
        $configs = [];
        foreach ($modules as $module) {
            $path = $this->getModulePath($module);
            if (!$path) {
                throw new \Exception(sprintf('Path for module %s not found', $module));
            }
            $configFile = $path . "/config/{$fileConfigName}.php";
            if (!file_exists($configFile)) {
                continue;
            }
            $moduleResources = include $configFile;
            if ($recursiveMerge) {
                $configs = ArrayTools::mergeRecursive($configs, $moduleResources);
            } else {
                $configs = array_merge($configs, $moduleResources);
            }
        }

        return $configs;
    }
}
