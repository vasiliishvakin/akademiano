<?php

namespace Akademiano\SimplaView;


use Akademiano\Config\Config;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\SimplaView\Exception\TemplateNotDefinedException;

abstract class AbstractView implements ViewInterface
{
    use EnvironmentIncludeTrait;

    const TPL_EXT = 'tpl';
    const THEMES_DIR = "themes";

    protected $render;

    /** @var  Config */
    protected $config;

    protected $vars = [];
    protected $globalVars = [];
    protected $template;
    protected $templateExtension = self::TPL_EXT;
    protected $arrayTemplates;
    protected $templateDirs = [];

    public function getRootDir()
    {
        return ROOT_DIR;
    }

    /**
     * @param mixed $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $templateExtension
     */
    public function setTemplateExtension($templateExtension)
    {
        $this->templateExtension = $templateExtension;
    }

    /**
     * @return string
     */
    public function getTemplateExtension()
    {
        return $this->templateExtension;
    }

    public function setTemplate($name)
    {
        $this->template = $name;
    }

    /**
     * @return mixed
     */
    public function getTemplate(): ?string
    {
        if (!empty($this->template)) {
            $template =  $this->template . '.' . $this->getTemplateExtension();
            return  $template;
        }
        return null;
    }

    public function setArrayTemplates($templates)
    {
        $this->arrayTemplates = $templates;
    }

    public function addArrayTemplate($templateString, $name = self::DEFAULT_TEMPLATE)
    {
        $this->arrayTemplates[$name] = $templateString;
    }

    /**
     * @return mixed
     */
    public function getArrayTemplates()
    {
        return $this->arrayTemplates;
    }

    public function assign($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function assignArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->assign($key, $value);
        }
    }

    public function getAssignedVars()
    {
        return $this->vars;
    }

    public function addGlobalVar($name, $value)
    {
        $this->globalVars[$name] = $value;
    }

    /**
     * @return array
     */
    public function getGlobalVars()
    {
        return $this->globalVars;
    }

    /**
     * @param array $templateDirs
     */
    public function setTemplateDirs(array $templateDirs)
    {
        $this->templateDirs = $templateDirs;
    }

    public function addTemplateDir($directory, $isAppend = true)
    {
        if ($isAppend) {
            $this->templateDirs[] = $directory;
        } else {
            array_unshift($this->templateDirs, $directory);
        }
    }

    public function addTemplateDirs(array $directories, $isAppend = true)
    {
        foreach ($directories as $directory){
            $this->addTemplateDir($directory, $isAppend);
        }
    }

    /**
     * @return array
     */
    public function getTemplateDirs()
    {
        return $this->templateDirs;
    }

    public function filterContent($content)
    {
        return $content;
    }

    abstract public function render($params = [], $templateName = null);
}
