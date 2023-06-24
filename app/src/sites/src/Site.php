<?php


namespace Akademiano\Sites;


use Akademiano\SimplaView\AbstractView;
use Akademiano\Sites\Site\ConfigDir;
use Akademiano\Sites\Site\DataStorage;
use Akademiano\Sites\Site\Directory;
use Akademiano\Sites\Site\DirectoryInterface;
use Akademiano\Sites\Site\PublicStorage;
use Akademiano\Sites\Site\Theme;
use Akademiano\Sites\Site\ThemesDir;
use Akademiano\Utils\FileSystem;
use Composer\Autoload\ClassLoader;

abstract class Site implements SiteInterface
{
    const NAMESPASE_PART_PREFIX = "\\Site\\";
    const NAMESPASE_PART_PREFIX_LENGTH = 6;
    const NAMESPASE_PART_SUFFIX_LENGTH = 5;

    protected $rootDir;

    protected $publicDir;

    protected $name;

    protected $path;

    /** @var  DataStorage */
    protected $dataStorage;

    protected $dataGlobalPath;

    protected $publicGlobalPath;

    protected $publicWebPath;

    /** @var  PublicStorage */
    protected $publicStorage;

    /** @var  ClassLoader */
    protected $loader;

    /** @var  ThemesDir */
    protected $themesDir;

    /** @var  ConfigDir */
    protected $configDir;

    /**
     * Site constructor.
     * @param ClassLoader $loader
     */
    public function __construct(ClassLoader $loader)
    {
        $this->setLoader($loader);
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
    public function setLoader($loader)
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
                throw new \LogicException("Root dir is not defined");
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

    /**
     * @return mixed
     */
    public function getPublicDir()
    {
        if (null === $this->publicDir) {
            if (defined("PUBLIC_DIR")) {
                $this->publicDir = PUBLIC_DIR;
            } else {
                $publicDir = realpath($this->getRootDir() . DIRECTORY_SEPARATOR . PublicStorage::GLOBAL_DIR);
                if (!$publicDir && !is_dir($publicDir)) {
                    throw new \LogicException("Public dir is not defined");
                }
                $this->publicDir = $publicDir;
            }
        }
        return $this->publicDir;
    }

    /**
     * @param mixed $publicDir
     */
    public function setPublicDir($publicDir)
    {
        $this->publicDir = $publicDir;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = strtolower(
                substr(get_class($this), $prefixPos = strpos(get_class($this), "\\") + 1, strrpos(get_class($this), "\\") - $prefixPos)
            );
        }
        return $this->name;
    }

    public function getNamespace()
    {
        return "\\" . substr(get_class($this), 0, strrpos(get_class($this), "\\"));
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    protected function getPath()
    {
        if (null === $this->path) {
            $class = get_class($this);
            $file = $this->getLoader()->findFile($class);
            $dir = realpath(dirname($file));
            $this->path = $dir;
        }
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    /**
     * @return DirectoryInterface
     */
    public function getDataStorage()
    {
        if (null === $this->dataStorage) {
            $dataInternalPath = realpath($this->getPath() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . DataStorage::INTERNAL_DIR);
            if (!is_dir($dataInternalPath)) {
                $this->dataStorage = false;
            } else {
                $globalPath = $this->getDataGlobalPath();
                if (!FileSystem::inDir($dataInternalPath, $globalPath, false)) {
                    $dataStorage = new DataStorage($dataInternalPath, $globalPath);
                } else {
                    $dataStorage = new Directory($dataInternalPath);
                }
                $this->dataStorage = $dataStorage;
            }
        }
        return (false !== $this->dataStorage) ? $this->dataStorage : null;
    }

    /**
     * @param DataStorage $dataStorage
     */
    public function setDataStorage($dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * @return mixed
     */
    public function getDataGlobalPath()
    {
        if (null === $this->dataGlobalPath) {
            $dataGlobalPath = $this->getRootDir() . DIRECTORY_SEPARATOR . DataStorage::GLOBAL_DIR
                . DIRECTORY_SEPARATOR . $this->getName();
            $this->dataGlobalPath = $dataGlobalPath;
        }
        return $this->dataGlobalPath;
    }


    /**
     * @return mixed
     */
    public function getPublicGlobalPath()
    {
        if (null === $this->publicGlobalPath) {
            $publicGlobalPath = $this->getRootDir() . DIRECTORY_SEPARATOR . PublicStorage::GLOBAL_DIR
                . DIRECTORY_SEPARATOR . $this->getName();
            $this->publicGlobalPath = $publicGlobalPath;
        }
        return $this->publicGlobalPath;
    }

    /**
     * @param mixed $publicGlobalPath
     */
    public function setPublicGlobalPath($publicGlobalPath)
    {
        $this->publicGlobalPath = $publicGlobalPath;
    }

    /**
     * @return mixed
     */
    public function getPublicWebPath()
    {
        if (null === $this->publicWebPath) {
            if (!$this->getPublicGlobalPath()) {
                throw new \RuntimeException(sprintf('Not exist public store directory'));
            }
            $this->publicWebPath = "/" . PublicStorage::GLOBAL_DIR . "/" . $this->getName();
        }
        return $this->publicWebPath;
    }

    /**
     * @param mixed $publicWebPath
     */
    public function setPublicWebPath($publicWebPath)
    {
        $this->publicWebPath = $publicWebPath;
    }

    /**
     * @return PublicStorage|null
     */
    public function getPublicStorage()
    {
        if (null === $this->publicStorage) {
            $publicInternalPath = realpath($this->getPath() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . PublicStorage::INTERNAL_DIR);
            if (!is_dir($publicInternalPath)) {
                $this->publicStorage = false;
            } else {
                $globalPath = $this->getPublicGlobalPath();
                if (!FileSystem::inDir($this->getPublicDir(), $publicInternalPath, false)) {
                    $publicStorage = new PublicStorage($publicInternalPath, $globalPath, $this->getPublicWebPath());
                } else {
                    $publicStorage = new Directory($publicInternalPath);
                }
                $this->publicStorage = $publicStorage;
            }
        }
        return (false !== $this->publicStorage) ? $this->publicStorage : null;
    }

    /**
     * @param PublicStorage $publicStorage
     */
    public function setPublicStorage(PublicStorage $publicStorage)
    {
        $this->publicStorage = $publicStorage;
    }

    /**
     * @return ThemesDir
     */
    public function getThemesDir()
    {
        if (null === $this->themesDir) {
            $themesPath = $this->getPath() . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR;
            if (!is_dir($themesPath)) {
                $this->themesDir = false;
            } else {
                $themesDir = new ThemesDir($themesPath);
                $this->themesDir = $themesDir;
            }
        }
        return (false !== $this->themesDir) ? $this->themesDir : null;
    }

    /**
     * @param mixed $themesDir
     */
    public function setThemesDir(ThemesDir $themesDir)
    {
        $this->themesDir = $themesDir;
    }

    /**
     * @param $theme
     * @return Theme|null
     */
    public function getTheme($theme)
    {
        if ($this->getThemesDir()) {
            return $this->getThemesDir()->getTheme($theme);
        }
    }

    /**
     * @return ConfigDir
     */
    public function getConfigDir()
    {
        if (null === $this->configDir) {
            $configPath = $this->getPath() . DIRECTORY_SEPARATOR . \Akademiano\Config\ConfigLoader::NAME_CONFIG;
            if (!is_dir($configPath) || !is_readable($configPath)) {
                $this->configDir = false;
            } else {

                $this->configDir = new ConfigDir(
                    $configPath,
                    \Akademiano\Config\FS\ConfigDir::LEVEL_DEFAULT,
                    [
                        "siteNamespace" => $this->getNamespace(),
                        "siteName" => $this->getName(),
                    ]
                );
            }
        }
        return (false !== $this->configDir) ? $this->configDir : null;
    }

    /**
     * @param ConfigDir $configDir
     */
    public function setConfigDir($configDir)
    {
        $this->configDir = $configDir;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
