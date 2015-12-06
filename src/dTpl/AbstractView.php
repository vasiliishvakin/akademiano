<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace dTpl;


abstract class AbstractView implements InterfaceView
{
    const TPL_EXT = 'tpl';

    protected $render;
    protected $config = [];

    protected $vars = [];
    protected $globalVars = [];
    protected $template;
    protected $templateExtension = self::TPL_EXT;
    protected $arrayTemplates;
    protected $templateDirs = [];
    protected $realTemplateDirs;

    public static function mergeRecursive()
    {
        $arrays = func_get_args();
        $merged = array_shift($arrays);
        foreach ($arrays as $currentArray) {
            foreach ($currentArray as $key => $value) {
                if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::mergeRecursive($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }

    public function getRootDir()
    {
        $rootDir = defined('ROOT_DIR') ? ROOT_DIR : null;
        if (is_null($rootDir)) {
            $rootDir = realpath(__DIR__ . '/../../../../../');
            define('ROOT_DIR', $rootDir);
        }
        return $rootDir;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
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
    public function getTemplate()
    {
        return $this->template . '.' . $this->getTemplateExtension();
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

    public function addTemplateDir($directory, $append = true)
    {
        if ($append) {
            $this->templateDirs[] = $directory;
        } else {
            array_unshift($this->templateDirs, $directory);
        }
    }

    /**
     * @return array
     */
    public function getTemplateDirs()
    {
        $dirs = [];
        if (null === $this->realTemplateDirs) {
            $config = $this->getConfig();
            if (!isset($config["themesDir"])) {
                if (file_exists(ROOT_DIR . "/themes")) {
                    $config["themesDir"] = "themes";
                }
            }
            if (isset($config["theme"])) {
                if (!isset($config["theme"]) && file_exists(ROOT_DIR . "/" . $config["themes"] . "/" . "default")) {
                    $config["theme"] = "default";
                }
                $dirs[] = $config["themesDir"] . "/" . $config["theme"];
            } else {
                $dirs[] = isset($config['templateDirs']) ? $config['templateDirs'] : 'templates';
            }

            if (isset($config["themesDir"]) && $config["theme"] !== "default") {
                if (file_exists(ROOT_DIR . "/" . $config["themesDir"] . "/default")) {
                    $dirs[] = $config["themesDir"] . "/default";
                }
            }

            if (is_object($dirs) && method_exists($dirs, 'toArray')) {
                $dirs = $dirs->toArray();
            }
            $dirs = array_merge($dirs, $this->templateDirs);
            $realDirs = [];
            foreach ($dirs as $dir) {
                if (strpos($dir, '/') === 0) {
                    $realDirs[] = $dir;
                    continue;
                }
                $rootDir = $this->getRootDir();
                $dir = realpath($rootDir . '/' . $dir);
                if ($dir && is_dir($dir)) {
                    $realDirs[] = $dir;
                }
            }
            $this->realTemplateDirs = $realDirs;
        }
        return $this->realTemplateDirs;
    }

    public function filterContent($content)
    {
        return $content;
    }

    abstract public function render($params = [], $templateName = null);

} 