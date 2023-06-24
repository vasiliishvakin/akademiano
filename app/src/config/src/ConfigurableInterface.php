<?php

namespace Akademiano\Config;



interface ConfigurableInterface extends ConfigInterface
{
    /**
     * @param Config $config
     */
    public function setConfig(Config $config);

}
