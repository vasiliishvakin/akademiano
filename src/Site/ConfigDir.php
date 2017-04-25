<?php


namespace Akademiano\Sites\Site;


class ConfigDir extends \Akademiano\Config\FS\ConfigDir implements DirectoryInterface
{

    public function __construct($path, $level = \Akademiano\Config\FS\ConfigDir::LEVEL_DEFAULT, array $params=null)
    {
        parent::__construct($path, $level, $params);
    }

    public function getFile($fileName)
    {
        throw new \LogicException(sprintf('Method "%s" in class "%s" not implemented now', __METHOD__, __CLASS__));
    }

    public function __toString()
    {
        return $this->getPath();
    }
}
