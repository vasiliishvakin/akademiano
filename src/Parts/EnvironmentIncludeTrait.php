<?php


namespace Akademiano\HttpWarp\Parts;


use Akademiano\HttpWarp\Environment;

trait EnvironmentIncludeTrait
{
    /** @var  Environment */
    protected $environment;

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        if (null === $this->environment) {
            $this->environment = new Environment();
        }
        return $this->environment;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }
}
