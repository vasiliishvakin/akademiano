<?php


namespace Akademiano\Sites;


use Akademiano\Sites\Site\DataStorage;
use Akademiano\Sites\Site\PublicStorage;
use Akademiano\Sites\Site\ThemesDir;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface SiteInterface extends StringableInterface
{

    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    public function getNamespace();

    /**
     * @return DataStorage
     */
    public function getDataStorage();

    /**
     * @return string
     */
    public function getPublicGlobalPath();

    /**
     * @return string
     */
    public function getPublicWebPath();

    /**
     * @return PublicStorage|null
     */
    public function getPublicStorage();

    /**
     * @return ThemesDir
     */
    public function getThemesDir();

}
