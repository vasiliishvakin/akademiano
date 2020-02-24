<?php


namespace Akademiano\Config\Permanent;


use Akademiano\Config\Config;

class PermanentFabric
{
    public function build(Config $config, PermanentStorageInterface $storage, ?array $prefix): PermanentConfig
    {
        switch (true) {
            case ($storage instanceof PermanentStorageFile):
                return $this->toPermanentFile($config, $storage, $prefix);
            default:
                throw new \LogicException(sprintf('Storage format %s not implemented', get_class($storage)));
        }
    }

    public function toPermanentFile(Config $config, PermanentStorageFile $storage, ?array $prefix): PermanentConfigFile
    {
        return new PermanentConfigFile($config, $storage->getFile(), $prefix);
    }
}
