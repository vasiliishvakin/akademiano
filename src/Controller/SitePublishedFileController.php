<?php


namespace Akademiano\Sites\Controller;


use Akademiano\Core\Controller\AbstractController;
use Akademiano\Sites\Site;
use Akademiano\HttpWarp\Exception\NotFoundException;

class SitePublishedFileController extends AbstractController
{
    /**
     * @return Site
     */
    public function getCurrentSite()
    {
        return $this->getDiContainer()["currentSite"];
    }

    public function getSitePublicStore()
    {
        return $this->getCurrentSite()->getPublicStore();
    }

    public function getFile($path)
    {
        $store = $this->getSitePublicStore();
        if (!$store) {
            throw new NotFoundException(sprintf('File "%s" not found', $path));
        }
        $file = $store->getFile($path);
        if (!$file) {
            throw new NotFoundException('File "%s" not found', $path);
        }
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
