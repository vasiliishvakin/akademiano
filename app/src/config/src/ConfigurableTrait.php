<?php

namespace Akademiano\Config;


trait ConfigurableTrait
{
    /** @var  Config */
    protected $config;

    /**
     * @param array|string|null $path
     * @param mixed|null $default
     * @return Config|null
     */
    public function getConfig($path = null, $default = null)
    {
        if ($path)  {
            return $this->config->get($path, $default);
        }
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
