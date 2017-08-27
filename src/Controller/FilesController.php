<?php

namespace Akademiano\Content\Files\Controller;

use Akademiano\Content\Files\Api\v1\FilesApi;
use Akademiano\Content\Files\Model\File;
use Akademiano\Core\Controller\AkademianoController;
use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\HttpWarp\Header;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\FileSystem;

class FilesController extends AkademianoController
{
    const ENTITY_API_ID = FilesApi::API_ID;
    const INTERNAL_URL_PREFIX = 'files';
    const ENV_ACCEL_VAR_NAME = 'SERVER_ACCEL_HEADER';

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

        $isAccel = $this->getRequest()->getEnvironment()->getVar(self::ENV_ACCEL_VAR_NAME, false);

        if ($isAccel) {
            Header::accel($url, $path);
        } else {
            if (!FileSystem::inDir(DATA_DIR, $path) && !FileSystem::inDir(PUBLIC_DIR, $path)) {
                throw new AccessDeniedException(sprintf('Access Denied to no accel view file "%s" not in allowed dirs ("%s", %s)', $path, DATA_DIR, PUBLIC_DIR));
            }
            Header::mime($path);
            echo file_get_contents($path);
        }
    }
}
