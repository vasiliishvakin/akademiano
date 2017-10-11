<?php

namespace Akademiano\Core\View;

use Akademiano\Twig\Extensions\AssetExtension;
use Akademiano\Twig\Extensions\UrlExtension;
use Akademiano\User\AuthInterface;
use Akademiano\User\Twig\UserExtension;
use Akademiano\Config\Config;
use Akademiano\Core\Exception\InvalidConfigurationException;
use Akademiano\Router\Router;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Parts\DIContainerTrait;
use Akademiano\Utils\StringUtils;
use Akademiano\SimplaView\AbstractView;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\ArrayTools;

class TwigView extends AbstractView implements ViewInterface, DIContainerIncludeInterface
{
    protected $templateExtension = 'twig';

    protected $formCsrfProvider;
    protected $formValidator;
    protected $formFactory;

    protected $dataDir;
    protected $rootDir;

    use DIContainerTrait;

    public function reset()
    {
        unset($this->render);
        $this->$vars = [];
        $this->$globalVars = [];
        unset($this->template);
        $this->$templateExtension = self::TPL_EXT;
        $this->arrayTemplates = [];
        $this->templateDirs = [];
    }

    /**
     * @return mixed
     */
    public function getDataDir()
    {
        if (null == $this->dataDir) {
            $this->dataDir = DATA_DIR;
        }
        return $this->dataDir;
    }

    /**
     * @param mixed $dataDir
     */
    public function setDataDir($dataDir)
    {
        $this->dataDir = $dataDir;
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (null == $this->rootDir) {
            $this->rootDir = ROOT_DIR;
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



    public function initExtension($extension, \Twig_Environment $render, \Twig_Loader_Filesystem $fileSystemLoader)
    {

        if (0 !== strpos($extension, "\\")) {
            $extension = "\\" . $extension;
        }
        $config = $this->getConfig();

        switch ($extension) {
            case "\\Akademiano\\Twig\\Extensions\\UrlExtension" :
                $config = isset($config["urlExtension"]) ? $config["urlExtension"] : [];
                $routeGenerator = isset($config["routeGenerator"]) ? $config["routeGenerator"] : [new Router(), "getUrl"];
                $extension = new UrlExtension();
                $extension->setRouteGenerator($routeGenerator);
                break;
            case "\\Akademiano\\Twig\\Extensions\\AssetExtension" :
                $extension = new AssetExtension();
                $assetsPaths = ArrayTools::get($config, ["assetExtension", "paths"], $extension->getPaths());
                if ($assetsPaths instanceof Config) {
                    $assetsPaths = $assetsPaths->toArray();
                }
                $assetsTemplates = [];
                foreach ($this->getTemplateDirs() as $dir) {
                    $assetDir = $dir . DIRECTORY_SEPARATOR . AssetExtension::ASSETS_DIR;
                    if (is_dir($assetDir)) {
                        $assetsTemplates[$dir] = $dir;
                    }
                }

                $assetsPaths = array_merge($assetsTemplates, $assetsPaths);

                if (!empty($assetsPaths)) {
                    $extension->setPaths($assetsPaths);
                }
                break;
            case "\\Akademiano\\User\\Twig\\UserExtension" :
                $custodian = $config->get(["userExtension", "custodian"]);
                if (!$custodian instanceof AuthInterface) {
                    throw new InvalidConfigurationException("for userExtension please add custodian in config");
                }
                $extension = new UserExtension();
                $extension->setCustodian($custodian);
                break;
            default:
                $extName = $extension;
                $extension = new $extension;
                if (method_exists($extension, "setConfig")) {
                    $extConfig = ArrayTools::get($config, [lcfirst(StringUtils::cutClassName($extName))], []);
                    $extension->setConfig($extConfig);
                }
                if ($extension instanceof DIContainerIncludeInterface) {
                    $extension->setDiContainer($this->getDiContainer());
                }
        }
        return $extension;
    }

    /**
     * @return \Twig_Environment
     */
    public function getRender()
    {
        if (is_null($this->render)) {
            $config = $this->getConfig();
            $templateDirs = $this->getTemplateDirs();
            $loaderFs = new \Twig_Loader_Filesystem($templateDirs);
            $templatesArray = $this->getArrayTemplates();
            if (!empty($templateArrays)) {
                $arrayLoader = new \Twig_Loader_Array($templatesArray);
                $loader = new \Twig_Loader_Chain([$loaderFs, $arrayLoader]);
            }
            $options = isset($config['options']) ? $config['options'] : [];
            if ($options instanceof Config) {
                $options = $options->toArray();
            }
            if (isset($options['cache']) && $options['cache']) {
                if (is_bool($options['cache'])) {
                    $options['cache'] = 'cache/twig';
                }
                $cache = realpath($this->getDataDir() . '/' . $options['cache']);
                if ($cache) {
                    $options['cache'] = $cache;
                } else {
                    unset($options['cache']);
                }
            }
            $loader = isset($loader) ? $loader : $loaderFs;
            $this->render = new \Twig_Environment($loader, $options);

            $extensions = isset($config['extensions']) ? $config['extensions'] : [];
            if ($extensions instanceof Config) {
                $extensions = $extensions->toArray();
            }
            foreach ($extensions as $extension) {
                $extension = $this->initExtension($extension, $this->render, $loaderFs);
                $this->render->addExtension($extension);
            }
            $filters = isset($config['filters']) ? $config['filters'] : [];
            if ($filters instanceof Config) {
                $filters = $filters->toArray();
            }
            foreach ($filters as $name => $filter) {
                $callable = $filter[0];
                $params = isset($filter[1]) ? $filter[1] : [];
                $this->render->addFilter(new \Twig_SimpleFilter($name, $callable, $params));
            }
        }
        return $this->render;
    }

    public function render($params = [], $templateName = null)
    {
        if (!is_null($templateName)) {
            $this->setTemplate($templateName);
        }
        $vars = $this->getAssignedVars();
        $vars = ArrayTools::mergeRecursive($vars, $params);
        $globalVars = $this->getGlobalVars();
        $render = $this->getRender();
        foreach ($globalVars as $name => $value) {
            $render->addGlobal($name, $value);
        }
        /** @var \Twig_Environment $template */
        $template = $this->getTemplate();
        $output = $render->render($template, $vars);
        return $output;
    }

    public function exist($template)
    {
        $template = $template . "." . $this->getTemplateExtension();
        $loader = $this->getRender()->getLoader();
        $result = $loader->exists($template);
        return $result;
    }
}
