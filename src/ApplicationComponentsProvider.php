<?php


namespace Akademiano\Core;


use Akademiano\Config\Config;
use Akademiano\Config\ConfigLoader;
use Akademiano\Config\FS\ConfigDir;
use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Response;
use Akademiano\HttpWarp\Session;
use Akademiano\Router\Router;
use Akademiano\SimplaView\AbstractView;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\User\AuthInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Akademiano\Sites\SitesManager;

class ApplicationComponentsProvider implements ServiceProviderInterface
{
    const CONFIG_LEVEL_MODULES = -10;
    const CONFIG_LEVEL_SITES_SHARED = 10;
    const CONFIG_LEVEL_SITES_CURRENT = 20;

    public function register(Container $pimple)
    {
        if (!isset($pimple['sessions'])) {
            $pimple['sessions'] = function (Container $pimple) {
                return new Session();
            };
        }

        if (!isset($pimple['request'])) {
            $pimple['request'] = function (Container $pimple) {
                return new Request();
            };
        }

        if (!isset($pimple['response'])) {
            $pimple['response'] = function (Container $pimple) {
                return new Response();
            };
        }

        if (!isset($pimple['router'])) {
            $pimple['router'] = function (Container $pimple) {
                return new Router($pimple["request"]);
            };
        }

        if (!isset($pimple['baseConfigLoader'])) {
            $pimple['baseConfigLoader'] = function (Container $pimple) {
                $configLoader = new ConfigLoader($pimple);
                $postProcessors = $pimple["sitesManager"]->getConfigLoaderPostProcessors();
                foreach ($postProcessors as $configName => $processors) {
                    $processors = (array) $processors;
                    foreach ($processors as $processor) {
                        $configLoader->addPostProcessor($processor, $configName);
                    }
                }


                if (!empty($pimple["sharedSite"])) {
                    $configDir = $pimple["sharedSite"]->getConfigDir();
                    if ($configDir instanceof ConfigDir) {
                        $configLoader->attachConfigDir($configDir, self::CONFIG_LEVEL_SITES_SHARED);
                    }
                }
                if (!empty($pimple["currentSite"])) {
                    $configDir = $pimple["currentSite"]->getConfigDir();
                    if ($configDir instanceof ConfigDir) {
                        $configLoader->attachConfigDir($configDir, self::CONFIG_LEVEL_SITES_CURRENT);
                    }
                }
                return $configLoader;
            };
        }

        if (!isset($pimple['configLoader'])) {
            $pimple['configLoader'] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["baseConfigLoader"];
                /** @var ModuleManager $moduleManager */
                $moduleManager = $pimple["moduleManager"];

                $postProcessors = $moduleManager->getConfigLoaderPostProcessors();
                foreach ($postProcessors as $configName => $processors) {
                    $processors = (array) $processors;
                    foreach ($processors as $processor) {
                        $configLoader->addPostProcessor($processor, $configName);
                    }
                }

                $configDirs = $moduleManager->getConfigDirs();
                foreach ($configDirs as $dirInfo) {
                    if (is_array($dirInfo)) {
                        $path = (isset($dirInfo["path"])) ? $dirInfo["path"] : $dirInfo[0];
                        $params = (isset($dirInfo["params"])) ? $dirInfo["params"] : (isset($dirInfo[1]) ? $dirInfo[1] : null);
                    } else {
                        $path = $dirInfo;
                        $params= null;
                    }
                    $configLoader->addConfigDir($path, self::CONFIG_LEVEL_MODULES, $params);
                }

                return $configLoader;
            };
        }

        if (!isset($pimple["baseConfig"])) {
            $pimple["baseConfig"] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["baseConfigLoader"];
                return $configLoader->getConfig(ConfigLoader::NAME_CONFIG);
            };
        }

        if (!isset($pimple["config"])) {
            $pimple["config"] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["configLoader"];
                return $configLoader->getConfig(ConfigLoader::NAME_CONFIG);
            };
        }

        if (!isset($pimple["resources"])) {
            $pimple["resources"] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["configLoader"];
                return $configLoader->getConfig(Application::CONFIG_NAME_RESOURCES);
            };
        }

        if (!isset($pimple["routes"])) {
            $pimple["routes"] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["configLoader"];
                return $configLoader->getConfig(Router::CONFIG_NAME);
            };
        }


        if (!isset($pimple["environment"])) {
            $pimple["environment"] = function (Container $pimple) {
                return new Environment();
            };
        }

        if (!isset($pimple['modulesList'])) {
            $pimple['modulesList'] = function (Container $pimple) {
                /** @var Config $config */
                $config = $pimple["baseConfig"];
                return $config->get("modules", [])->toArray();
            };
        }

        if (!isset($pimple["moduleManager"])) {
            $pimple["moduleManager"] = function (Container $pimple) {
                $modulesList = $pimple["modulesList"];
                $mm = new ModuleManager($modulesList, $pimple);
                $mm->setLoader($pimple["loader"]);
                return $mm;
            };
        }

        if (!isset($pimple["view"])) {
            $pimple["view"] = function (Container $pimple) {
                return $this->getView($pimple);
            };
        }

        $pimple["currentUser"] = function (Container $pimple) {
            if (!isset($pimple['custodian'])) {
                return null;
            }
            /** @var AuthInterface $custodian */
            $custodian = $pimple['custodian'];

            return $custodian->getCurrentUser();
        };

        $pimple["sitesManager"] = function (Container $pimple) {
            return new SitesManager($pimple["loader"], $pimple["environment"]);
        };

        $pimple["currentSite"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->getCurrentSite();
        };

        $pimple["sharedSite"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->getSite(SitesManager::SITE_SHARED);
        };

        $pimple["applicationComponents"] = true;
    }

    public function getView(Container $pimple)
    {
        /** @var Config $config */
        $config = $pimple["config"];
        $viewConfig = $config->get('view', []);
        $viewAdapter = $viewConfig->get('adapter', 'Twig');

        /** @var ViewInterface view */
        $view = ViewFactory::getView($viewAdapter, $viewConfig);
        //set templates dir
        if ($view instanceof AbstractView) {
            //themes
            $theme = $viewConfig->get("theme", "default");
            $themeContainedDirs = [];
            if ($theme !== "default") {
                if ($pimple["currentSite"] && ($themeDir = $pimple["currentSite"]->getTheme($theme))) {
                    $themeContainedDirs[] = (string)$themeDir;
                }
                if ($pimple["sharedSite"] && ($themeDir = $pimple["sharedSite"]->getTheme($theme))) {
                    $themeContainedDirs[] = (string)$themeDir;
                }

                if (empty($themeContainedDirs)) {
                    throw  new \RuntimeException("Theme {$theme} not exist");
                }
            }

            if ($pimple["currentSite"] && ($themeDir = $pimple["currentSite"]->getTheme("default"))) {
                $themeContainedDirs[] = (string)$themeDir;
            }
            if ($pimple["sharedSite"] && ($themeDir = $pimple["sharedSite"]->getTheme("default"))) {
                $themeContainedDirs[] = (string)$themeDir;
            }
            if (empty($themeContainedDirs)) {
                throw  new \RuntimeException("No any theme not exist");
            }
            $view->addTemplateDirs($themeContainedDirs);

            //module templates
            /** @var ModuleManager $mm */
            $mm = $pimple["moduleManager"];
            $modules = $mm->getModulesList();
            foreach ($modules as $moduleName) {
                $templatesPath = $mm->getModulePath($moduleName) . "/templates";
                if (file_exists($templatesPath)) {
                    $view->addTemplateDir($templatesPath);
                }
            }
        }
        $viewVars = $viewConfig->get(["vars"]);
        if ($viewVars instanceof Config) {
            $viewVars = $viewVars->toArray();
        }
        if (!empty($viewVars) && is_array($viewVars)) {
            foreach ($viewVars as $name => $value) {
                if (is_callable($value)) {
                    $value = call_user_func($value, $this);
                }
                $view->assign($name, $value);
            }
        }
        return $view;
    }
}
