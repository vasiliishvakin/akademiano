<?php


namespace Akademiano\Core;


use Akademiano\Config\Config;
use Akademiano\Config\ConfigLoader;
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

class ApplicationComponentsProvider implements ServiceProviderInterface
{
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

        if (!isset($pimple['configLoader'])) {
            $pimple['configLoader'] = function (Container $pimple) {
                return new ConfigLoader($pimple);
            };
        }

        if (!isset($pimple["config"])) {
            $pimple["config"] = function (Container $pimple) {
                /** @var ConfigLoader $configLoader */
                $configLoader = $pimple["configLoader"];
                return $configLoader->getConfig(ConfigLoader::NAME_CONFIG);
            };
        }

        if (!isset($pimple["environment"])) {
            $pimple["environment"] = function (Container $pimple) {
                return new Environment();
            };
        }

        if (!isset($pimple["moduleManager"])) {
            $pimple["moduleManager"] = function (Container $pimple) {
                /** @var Config $config */
                $config = $pimple["config"];
                $modulesList = $config->get("modules", [])->toArray();
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
            return new SitesManager($pimple);
        };

        $pimple["currentSite"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->getCurrentSite();
        };

        $pimple["currentSiteDir"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->getCurrentSiteDir();
        };

        $pimple["sharedSiteDir"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->getSiteDir("all");
        };

        $pimple["isCurrentSiteDirDefault"] = function (Container $pimple) {
            /** @var SitesManager $sitesManager */
            $sitesManager = $pimple["sitesManager"];
            return $sitesManager->isCurrentSiteDirDefault();
        };

        $pimple["applicationComponents"] = true;
    }

    public function getView(Container $pimple)
    {
        /** @var Config $config */
        $config = $pimple["config"];
        $viewConfig = $config->get('view');
        $viewAdapter = $viewConfig->get('adapter', 'Twig');

        /** @var ViewInterface view */
        $view = ViewFactory::getView($viewAdapter, $viewConfig);
        //set templates dir
        if ($view instanceof AbstractView) {
            //themes
            $themeContainedDirs = [];
            $theme = $viewConfig->get("theme", "default");
            if ($theme !== "default") {
                $themePath = null;
                $siteThemeDirs = [];
                if ($pimple["currentSiteDir"]) {
                    $siteThemeDirs[] = $pimple["currentSiteDir"] . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR . DIRECTORY_SEPARATOR . $theme;
                }
                if ($pimple["sharedSiteDir"]) {
                    $siteThemeDirs[] = $pimple["sharedSiteDir"] . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR . DIRECTORY_SEPARATOR . $theme;
                }
                foreach ($siteThemeDirs as $dir) {
                    if (is_dir($dir)) {
                        $themePath = $dir;
                        break;
                    }
                }
                if (null === $themePath) {
                    throw  new \RuntimeException("Theme {$theme} not exist");
                }
                $themeContainedDirs[] = $themePath;
            }

            //default theme dirs
            $defaultThemeDirs = [];
            if ($pimple["currentSiteDir"]) {
                $defaultThemeDirs[] = $pimple["currentSiteDir"] . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR . DIRECTORY_SEPARATOR . "default";
            }
            if ($pimple["sharedSiteDir"]) {
                $defaultThemeDirs[] = $pimple["sharedSiteDir"] . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR . DIRECTORY_SEPARATOR . "default";
            }

            $defaultThemeDirs = array_filter($defaultThemeDirs, function ($dir) {
                return is_dir($dir);
            });
            $themeContainedDirs = $themeContainedDirs + $defaultThemeDirs;
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
