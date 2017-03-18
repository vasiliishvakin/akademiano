<?php


namespace Akademiano\Core;


use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Parts\DIContainerTrait;

class SitesManager implements DIContainerIncludeInterface
{
    use DIContainerTrait;

    protected $currentSite;

    public function getCurrentSite()
    {
        /** @var \Akademiano\HttpWarp\Environment $environment */
        $environment = $this->getDiContainer()["environment"];
        $site = $environment->getServerName();
        $site = strtolower(str_replace(["/", "\\", ".."], "", trim($site)));
        return $site;

    }

    public function getSiteDir($siteName)
    {

        $siteName = str_replace([".", "-"], "_", $siteName);
        if (substr($siteName, 0, 1) === "_") {
            $siteName = "_" . ucfirst(substr($siteName, 1));
        } else {
            $siteName = ucfirst($siteName);
        }
        $siteClass = "Sites\\" . $siteName . "\\Site";
        if (!class_exists($siteClass)) {
            return null;
        }

        $file = $this->getDiContainer()["loader"]->findFile($siteClass);
        $dir = realpath(dirname($file));
        return $dir;
    }

    public function isCurrentSiteDirDefault()
    {
        $siteName = $this->getDiContainer()["currentSite"];
        if ($this->getSiteDir($siteName)) {
            return false;
        } elseif ($this->getSiteDir("_default")) {
            return true;
        } else {
            return false;
        }
    }

    public function getCurrentSiteDir()
    {
        $siteName = $this->getDiContainer()["currentSite"];
        return $this->getSiteDir($siteName) ?: $this->getSiteDir("_default");
    }
}
