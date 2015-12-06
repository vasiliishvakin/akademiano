<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 27.10.2015
 * Time: 15:40
 */

namespace DeltaTwigExt;


use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\BaseAsset;
use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Filter\CssEmbedFilter;
use Assetic\Filter\FilterCollection;
use Assetic\Util\FilesystemUtils;
use DeltaCore\Parts\Configurable;
use DeltaUtils\FileSystem;
use DeltaUtils\StringUtils;
use HttpWarp\Environment;

class AssetExtension extends \Twig_Extension
{
    use Configurable;

    protected $rootDir;
    protected $publicDir;
    /** @var  Array */
    protected $paths;

    /** @var  AssetManager */
    protected $assetManager;
    protected $outputDir;
    protected $webPublic;
    protected $webOutput;
    /** @var  Environment */
    protected $environment;

    public function getName()
    {
        return 'delta_asset';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                "asset_css",
                [$this, "assetCss"],
                [
                    "is_safe" => ["html"],
                ]
            ),
            new \Twig_SimpleFunction(
                "asset_js",
                [$this, "assetJs"],
                [
                    "is_safe" => ["html"],
                ]
            ),
            new \Twig_SimpleFunction(
                "asset_img",
                [$this, "assetImg"]
            )
        ];
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (empty($this->rootDir)) {
            $this->rootDir = rtrim(realpath(__DIR__ . "/../../../../"), "/");
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
        if (is_null($this->publicDir)) {
            $this->publicDir = $this->getRootDir() . DIRECTORY_SEPARATOR . "public";
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
    public function getOutputDir()
    {
        if (is_null($this->outputDir)) {
            $this->outputDir = $this->getPublicDir() . DIRECTORY_SEPARATOR . "assets/compiled";
        }

        return $this->outputDir;
    }

    /**
     * @param mixed $outputDir
     */
    public function setOutputDir($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    /**
     * @return mixed
     */
    public function getWebPublic()
    {
        if (is_null($this->webPublic)) {
            $env = $this->getEnvironment();
            $port = ($env->getPort() !== 80 && $env->getPort() !== 443) ? ":" . $env->getPort() : "";
            $this->webPublic = $env->getScheme() . "://" . $env->getServerName() . $port;
        }

        return $this->webPublic;
    }

    /**
     * @param mixed $webPublic
     */
    public function setWebPublic($webPublic)
    {
        $this->webPublic = $webPublic;
    }

    /**
     * @return mixed
     */
    public function getWebOutput()
    {
        if (is_null($this->webOutput)) {
            $this->webOutput = $this->getWebPublic() . "/assets/compiled";
        }

        return $this->webOutput;
    }

    /**
     * @param mixed $webPath
     */
    public function setWebPath($webOutput)
    {
        $this->webOutput = $webOutput;
    }

    /**
     * @return Array
     */
    public function getPaths()
    {
        if (is_null($this->paths)) {
            $this->paths = [
                $this->getRootDir() . "/public/assets/vendor",
                $this->getRootDir() . "/public",
                $this->getRootDir() . "/vendor/",
            ];
        }

        return $this->paths;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        if (is_null($this->environment)) {
            $this->environment = new Environment();
        }

        return $this->environment;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Array $paths
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    public function setPath($path, $name = null)
    {
        if (is_null($this->paths)) {
            $this->getPaths();
        }
        if (is_null($name)) {
            $this->paths[$name] = $path;
        } else {
            $this->paths[] = $path;
        }
    }

    /**
     * @return AssetManager
     */
    public function getAssetManager()
    {
        if (is_null($this->assetManager)) {
            $this->assetManager = new AssetManager();
        }

        return $this->assetManager;
    }

    /**
     * @param AssetManager $assetManager
     */
    public function setAssetManager($assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function findFile($file)
    {
        foreach ($this->getPaths() as $path) {
            if (strpos($path, "/") !== 0) {
                $path = $this->getRootDir() . DIRECTORY_SEPARATOR . $path;
            }
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filePath)) {
                return $filePath;
            }
        }
    }

    public function loadAsset($file)
    {
        $filePath = $this->findFile($file);
        if (empty($filePath)) {
            throw new AssetException("Asset {$file} not found");
        }
        $asset = new FileAsset($filePath);

        return $asset;
    }

    public function loadAssets($files)
    {
        $files = (array)$files;
        $am = $this->getAssetManager();
        $assets = [];
        foreach ($files as $name => $file) {
            if (StringUtils::isText($name)) {
                if ($am->has($name)) {
                    $asset = new AssetReference($am, $name);
                } else {
                    $asset = $this->loadAsset($file);
                    $am->set($name, $asset);
                }
            } else {
                $asset = $this->loadAsset($file);
            }
            $assets[] = $asset;
        }
        if (empty($assets)) {
            throw new AssetException("Empty assets");
        }
        $ac = new AssetCollection($assets);

        return $ac;
    }

    public function writeAsset(BaseAsset $asset, $path = null, $prefix = null)
    {
        if (null === $path) {
            $path = $this->getOutputDir() . DIRECTORY_SEPARATOR . basename($asset->getSourcePath());
        }
        if (null !== $prefix) {
            $path = dirname($path) . DIRECTORY_SEPARATOR . $prefix . basename($path);
        }

        if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0750, true)) {
            throw new \RuntimeException('Unable to create directory ' . $dir);
        }

        if (false === @file_put_contents($path, $asset->dump(), LOCK_EX)) {
            throw new \RuntimeException('Unable to write file ' . $path);
        }

        return $path;
    }

    public function processAssets($files, $filters = null, $debug = false)
    {
        $webPatches = [];
        if ($debug) {
            $ac = $this->loadAssets($files, $filters, $debug);
            /*$fc = new FilterCollection([
                new CssEmbedFilter()
            ]);*/
            /** @var FileAsset $asset */
            foreach ($ac as $asset) {
                $filePath = $asset->getSourceDirectory();
                if (false !== $subDir = FileSystem::getSubDir($this->getPublicDir(), $filePath)) {
                    $subDir = !empty($subDir) ? $subDir . DIRECTORY_SEPARATOR : "";
                    $webPatches[] = $this->getWebPublic() . DIRECTORY_SEPARATOR . $subDir . basename($asset->getSourcePath());
                } else {
                    $filePath = $this->writeAsset($asset);
                    $subDir = FileSystem::getSubDir($this->getOutputDir(), dirname($filePath));
                    $subDir = !empty($subDir) ? $subDir . DIRECTORY_SEPARATOR : "";
                    $webPatches[] = $this->getWebOutput() . DIRECTORY_SEPARATOR . $subDir . basename($filePath);
                }
            }

        } else {
            throw new \LogicException("Only debug mode support");
            //получили коллекцию ассетов
            /* $fileName = hash("md5", implode('|', $files)) . "css";
             $pubDir = $this->getOutputDir();
             $filePath = $pubDir . DIRECTORY_SEPARATOR . $fileName;
             $ac = $this->loadAssets($files, $filters, $debug);*/
            //фильтры
        }

        return $webPatches;
    }

    public function assetCss($files, $filters = null, $debug = false)
    {
        $webPatches = $this->processAssets($files, $filters, $debug);
        $webPatches = array_map(function ($path) {
            return '<link rel="stylesheet" href="' . $path . '"/>';
        }, $webPatches);

        return implode("\n", $webPatches);
    }

    public function assetJs($files, $filters = null, $debug = false)
    {
        $webPatches = $this->processAssets($files, $filters, $debug);
        $webPatches = array_map(function ($path) {
            return '<script type=\'text/javascript\' src="' . $path . '"></script>';
        }, $webPatches);

        return implode("\n", $webPatches);
    }

    public function assetImg($file, $filters = null)
    {
        $webPatches = $this->processAssets($file, $filters, true);

        return reset($webPatches);
    }
}
