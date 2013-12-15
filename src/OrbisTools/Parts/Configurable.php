<?php

namespace OrbisTools\Parts;

use DeltaCore\Config;

trait Configurable
{
    /**
     * @var Config
     */
    protected $config;

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function getConfig($path = null, $default = null)
    {
        if (is_null($path)) {
            if (is_null($this->config)) {
                return new Config([]);
            }
            return $this->config;
        } else {
            return (!is_null($this->config)) ? $this->config->get($path, $default) : $default;
        }
    }

}