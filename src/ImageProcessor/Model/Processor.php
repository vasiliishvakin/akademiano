<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace ImageProcessor\Model;


use DeltaCore\Config;
use OrbisConvert\WebImage;

class Processor
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param \DeltaCore\Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return \DeltaCore\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $config = $this->getConfig();
        return $config->get(["ImageProcessor", "templates"], [])->toArray();
    }

    public function getTemplateParams($templateName)
    {
        $templates = $this->getTemplates();
        return isset($templates[$templateName]) ? $templates[$templateName] : null;
    }

    public function process($inFile, $outFile, $templateName)
    {
        $outDir = dirname($outFile);
        if (!file_exists($outDir)) {
            $result = mkdir($outDir, 0750, true);
            if (!$result) {
                throw new RuntimeException("Directory {$outDir} not created");
            }
        }
        $tplParams = $this->getTemplateParams($templateName);
        if (!$tplParams) {
            throw new \Exception("Template $templateName not defined");
        }
        $converter = new WebImage();
        $params =  isset($tplParams["options"]) ? $tplParams["options"] : [];
        array_unshift($params, $inFile, $outFile);
        $result = call_user_func_array([$converter, $tplParams["action"]], $params);
        if (!$result) {
            throw new \Exception("Convert not work");
        }
        $converter->optimize($outFile);
        return $outFile;
    }

} 