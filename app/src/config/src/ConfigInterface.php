<?php

namespace Akademiano\Config;


interface ConfigInterface
{
    const RESOURCE_ID = 'config';

    /**
     * @return Config|null
     */
    public function getConfig();

}
