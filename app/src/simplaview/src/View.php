<?php

namespace Akademiano\SimplaView;


use Akademiano\Utils\ArrayTools;

class View extends AbstractView implements ViewInterface
{
    public function getFilePath($file)
    {
        $dirs = $this->getTemplateDirs();
        foreach ($dirs as $dir) {
            $path = $dir . '/' . $file;
            if (is_readable($path)) {
                return $path;
            }
        }
        return null;
    }

    protected function processTemplate(array $vars = [])
    {
        $templateFile = $this->getTemplate();
        $arrayTemplates = $this->getArrayTemplates();
        $strTemplate = null;
        if (isset($arrayTemplates[$templateFile])) {
            $strTemplate = $arrayTemplates[$templateFile];
        };
        $content = null;
        if (!is_null($strTemplate)) {
            var_export($vars);
            try {
                ob_start();
                eval($strTemplate);
                $content = ob_get_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
        } else {
            $templatePath = $this->getFilePath($templateFile);
            if (is_null($templatePath)) {
                throw new \RuntimeException("Template $templateFile not exist in array templates and template dirs");
            }
            extract($vars);
            try {
                ob_start();
                include $templatePath;
                $content = ob_get_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
        }
        return $this->filterContent($content);
    }

    public function render($params = [], $templateName = null)
    {
        if (!is_null($templateName)) {
            $this->setTemplate($templateName);
        }
        $globalVars = $this->getGlobalVars();
        $vars = $this->getAssignedVars();
        $vars = ArrayTools::mergeRecursive($globalVars, $vars, $params);
        if (isset($vars['this'])) {
            unset($vars['this']);
        }
        return $this->processTemplate($vars);
    }

    public function exist($template)
    {
        $templateFile = $template . '.' . $this->getTemplateExtension();
        $arrayTemplates = $this->getArrayTemplates();
        if (isset($arrayTemplates[$templateFile])) {
            return true;
        };
        $path = $this->getFilePath($templateFile);
        return file_exists($path);
    }
}
