<?php


namespace Akademiano\Sites\Controller;


use Akademiano\Core\Controller\AkademianoController;
use Akademiano\Sites\Site;
use Akademiano\HttpWarp\Exception\NotFoundException;

class SitePublishedFileController extends AkademianoController
{
    /**
     * @return Site
     */
    public function getCurrentSite()
    {
        return $this->getDiContainer()["currentSite"];
    }

    /**
     * @return Site\PublicStore
     */
    public function getSitePublicStore()
    {
        return $this->getCurrentSite()->getPublicStorage();
    }

    public function getFile($path)
    {
        $store = $this->getSitePublicStore();
        if (!$store) {
            throw new NotFoundException(sprintf('File "%s" not found', $path));
        }
        $file = $store->getFileOrThrow($path);
        $file->sendContent();
    }

    public function indexAction($params)
    {
        $this->autoRenderOff();
        if(!isset($params["filePath"])) {
            throw new NotFoundException();
        }
        $filePath = $params["filePath"];
        $this->getFile($filePath);
    }

}
