<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Config\Config;

trait ConfigurableTrait
{
    protected $config = [];

    /**
     * @param array|Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function addConfig($config)
    {
        $currConfig = $this->getConfig();
        if (!$config  instanceof Config) {
            $config = new Config($config);
        }
        $this->config = $currConfig->joinLeft($config);
    }

    /**
     * @param null $path
     * @param null $default
     * @return Config
     */
    public function getConfig($path = null, $default = null)
    {
        if (!$this->config instanceof Config) {
            $this->config = new Config($this->config);
        }
        return $this->config->get($path, $default);
    }
}
