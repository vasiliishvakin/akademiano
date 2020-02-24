<?php


namespace Akademiano\Config\Permanent;


use Akademiano\Config\Config;
use Akademiano\Config\ConfigInterface;

abstract class PermanentConfig implements ConfigInterface
{
    /** @var Config */
    protected $config;

    /** @var bool */
    protected $isSaved = false;

    /** @var array|null */
    protected $prefix;

    public function __construct(Config $config, ?array $prefix)
    {
        $this->config = $config;
        $this->prefix = $prefix;
        register_shutdown_function([$this, 'save']);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    public function getPrefix(): ?array
    {
        return $this->prefix;
    }

    abstract public function save(): void;
}
