<?php


namespace Akademiano\Sites;


use Akademiano\Sites\Site\DataStore;
use Akademiano\Sites\Site\PublicStore;
use Akademiano\Sites\Site\ThemesDir;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface SiteInterface extends StringableInterface
{

    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @return DataStore
     */
    public function getDataStore();

    /**
     * @return mixed
     */
    public function getPublicGlobalPath();

    /**
     * @return mixed
     */
    public function getPublicWebPath();

    /**
     * @return PublicStore|null
     */
    public function getPublicStore();

    /**
     * @return ThemesDir
     */
    public function getThemesDir();

}
