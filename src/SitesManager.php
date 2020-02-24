<?php


namespace Akademiano\Sites;


use Akademiano\Router\Route;
use Akademiano\Router\Router;
use Akademiano\Sites\Exception\InvalidSiteNameException;
use Akademiano\Sites\Exception\NoAnySiteException;
use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Sites\Exception\NoSharedSiteException;
use Akademiano\Config\FS\ConfigDir;
use Composer\Autoload\ClassLoader;

class SitesManager implements EnvironmentIncludeInterface
{
    const SITE_DEFAULT = "_default";
    const SITE_SHARED = "all";

    use EnvironmentIncludeTrait;

    /** @var  ClassLoader */
    protected $loader;

    protected $rootDir;

    protected $currentSite;

    /** @var Site[] */
    protected $sites = [];


    public function __construct(ClassLoader $loader, Environment $environment)
    {
        if (null !== $loader) {
            $this->setLoader($loader);
        }
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
    }


    /**
     * @return ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param ClassLoader $loader
     */
    public function setLoader(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            if (defined("ROOT_DIR")) {
                $this->rootDir = ROOT_DIR;
            } else {
                throw new \RuntimeException("Root dir is not defined");
            }
        }
        return $this->rootDir;
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getPublishedRoutes()
    {
        return [
            "_sites_published_default_route" => [
                "patterns" => [
                    "type" => \Akademiano\Router\RoutePattern::TYPE_REGEXP,
                    "value" => "\/(?P<filePath>\w+\.\w+)$",
                ],
                "action" => [
                    [
                        "module" => __NAMESPACE__,
                        "controller" => "sitePublishedFile",
                    ],
                    "index"],
            ],
        ];
    }

    public function getConfigLoaderPostProcessors()
    {
        return [
            Router::CONFIG_NAME => [
                function (array $content, ConfigDir $dir, $name, $level) {
                    if ($name !== Router::CONFIG_NAME) {
                        return $content;
                    }
                    $siteN = $dir->getParams("siteName");
                    if (!$siteN) {
                        return $content;
                    }
                    $siteN = SitesManager::filterSiteName($siteN);
                    foreach ($content as $routeId => $route) {
                        $route = Route::normalize($route);
                        if (is_array($route["action"])) {
                            $route["action"] = [
                                [
                                    "site" => $siteN,
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

    public static function filterSiteName($name)
    {
        if (null === $name) {
            return null;
        }
        if (strtok($name, " /\\ ? * :") !== $name) {
            throw new InvalidSiteNameException(sprintf('Invalid site name %s', $name));
        }
        $name = strtolower(str_replace(["."], "_", trim($name)));
        if (substr($name, 0, 1) === "_") {
            $name = "_" . ucfirst(substr($name, 1));
        } else {
            $name = ucfirst($name);
        }
        return $name;
    }

    public function getSite($name)
    {
        if (null === $name) {
            return null;
        }
        $name = self::filterSiteName($name);
        if (!array_key_exists($name, $this->sites)) {
            $siteClass = "Sites\\" . $name . "\\Site";
            if (!class_exists($siteClass)) {
                $this->sites[$name] = false;
            } else {
                /** @var Site $site */
                $site = new $siteClass($this->getLoader());
                $site->setRootDir($this->getRootDir());
                $this->sites[$name] = $site;
            }
        }
        return (false !== $this->sites[$name]) ? $this->sites[$name] : null;
    }

    public function getSiteFromRootDir(): ?Site
    {
        $path = $this->getRootDir() . '/src/Site.php';
        $siteClass = null;
        $siteName = null;

        $classMap = $this->getLoader()->getClassMap();
        //try search in class map
        $classMapPath = $this->getRootDir() . '/vendor/composer/../../src/Site.php';
        $siteClass = array_search($classMapPath, $classMap, false);
        if (!$siteClass) {
            //try  get from readfile
            if (file_exists($path)) {
                $contents = file_get_contents($path);
                if (preg_match('~(namespace)(\\s+)([A-Za-z0-9\\\\]+?)(\\s*);~sm', $contents, $m)) {
                    $siteClass = $m[3];
                }
            }
        }
        if ($siteClass) {
            if (preg_match('~^Sites\\\\+([\w_]+)\\\\?~', $siteClass, $m)) {
                $siteName = $m[1];
            }
        }
        return  $siteName ? $this->getSite($siteName) : null;
    }

    public
    function getCurrentSite()
    {
        if (null === $this->currentSite) {
            //try to get site from serverName;
            $siteName = $this->getEnvironment()->getServerName();
            $site = $this->getSite($siteName);
            //try to get root dir site
            if (!$site) {
                $site = $this->getSiteFromRootDir();
            }
            //try to get default site
            if (!$site) {
                $site = $this->getSite(self::SITE_DEFAULT);
            }
            if (!$site) {
                throw new NoAnySiteException($siteName);
            }
            $this->currentSite = $site->getName();
        }
        return $this->getSite($this->currentSite);
    }

    public
    function getSharedSite()
    {
        $site = $this->getSite(self::SITE_SHARED);
        if (!$site) {
            throw new NoSharedSiteException();
        }
    }
}
