<?php

namespace Akademiano\Attach\Controller;

use Akademiano\Attach\Api\v1\FilesApi;
use Akademiano\Attach\Model\File;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\HttpWarp\Header;
use Akademiano\Utils\ArrayTools;

class FilesController extends AkademianoController
{
    const ENTITY_API_ID = FilesApi::API_ID;
    const INTERNAL_URL_PREFIX = 'files';

    /**
     * @return FilesApi
     */
    public function getEntityApy()
    {
        return $this->getDiContainer()[static::ENTITY_API_ID];
    }

    public function indexAction(array $params = null)
    {
        $this->autoRenderOff();
        $id = ArrayTools::getMaybe($params, "id")->getOrThrow(
            new \RuntimeException("Id to file not defined")
        );
        /** @var File $file */
        $file = $this->getEntityApy()->get($id)->getOrThrow(
            new NotFoundException(sprintf('File with id "%s" not found.', $id))
        );
        $url = '/' . static::INTERNAL_URL_PREFIX . '/' . $file->getPosition();
        $path = ROOT_DIR . DIRECTORY_SEPARATOR . $file->getPath();

        Header::accel($url, $path);
    }
}
