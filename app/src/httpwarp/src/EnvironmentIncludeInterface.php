<?php


namespace Akademiano\HttpWarp;


interface EnvironmentIncludeInterface
{
    /**
     * @return Environment
     */
    public function getEnvironment();

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment);

}
