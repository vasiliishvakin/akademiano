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

abstract class FilesController extends AkademianoController
{
    const ENTITY_API_ID = FilesApi::API_ID;
    const ENV_ACCEL_VAR_NAME = 'SERVER_ACCEL_HEADER';
    const ROUTE_FILE_BY_NAME = null;
    const INTERNAL_URL_PREFIX = 'data/files';

    /**
     * @return FilesApi
     */
    public function getEntityApy()
    {
        return $this->getDiContainer()[static::ENTITY_API_ID];
    }

    public function getRouteFileByName()
    {
        return static::ROUTE_FILE_BY_NAME;
    }

    public function idAction(array $params = null)
    {
        $this->autoRenderOff();
        $id = ArrayTools::getMaybe($params, "id")->getOrThrow(
            new \RuntimeException("Id to file not defined")
        );
        $id = hexdec($id);
        /** @var File $file */
        $file = $this->getEntityApy()->get($id)->getOrThrow(
            new NotFoundException(sprintf('File with id "%s" not found.', $id))
        );
        $this->redirect($this->getRouteFileByName(), ['id' => $file->getId()->getHex(), 'extension' => $file->getExtension(), 'format' => null]);
    }

    public function nameAction(array $params = null)
    {
        $this->autoRenderOff();

        $id = ArrayTools::getMaybe($params, "id")->getOrThrow(
            new \RuntimeException("Id to file not defined")
        );

        $id = hexdec($id);

        /** @var File $file */
        $file = $this->getEntityApy()->get($id)->getOrThrow(
            new NotFoundException(sprintf('File with id "%s" not found.', $id))
        );

        if (!FileSystem::inDir(DATA_DIR, $file->getFullPath()) && !FileSystem::inDir(PUBLIC_DIR, $file->getFullPath())) {
            throw new AccessDeniedException(sprintf('Access Denied to no accel view file "%s" not in allowed dirs ("%s", %s)', $file->getFullPath(), DATA_DIR, PUBLIC_DIR));
        }

        $template = $params['template'] ?? null;
        $extension = $params['extension'] ?? $file->getExtension();
        if ($template === '') {
            $template = null;
        }

        $outputFile = $this->getEntityApy()->formatFile($file, $extension, $template);

        if ($outputFile) {
            $path = ($this->getEntityApy()->isPublic() ? PUBLIC_DIR .DIRECTORY_SEPARATOR . "data" : DATA_DIR) . DIRECTORY_SEPARATOR . $outputFile->getPath();
            $url = '/' . static::INTERNAL_URL_PREFIX . '/' . $outputFile->getPosition();
        } else {
            $path = DATA_DIR. DIRECTORY_SEPARATOR . $file->getPath();
            $url = '/' . static::INTERNAL_URL_PREFIX . '/' . $file->getPosition() . '/' . $file->getId()->getInt() . '.' . $file->getExtension();
        }

        $isAccel = $this->getRequest()->getEnvironment()->getVar(self::ENV_ACCEL_VAR_NAME, false);

        if (Header::isSent()) {
            throw new \RuntimeException('Headers already sent');
        }

        Header::modified(filemtime($path));
        Header::cache();

        if ($isAccel) {
            Header::accel($url, $path);
        } else {
            Header::mime($path);
            echo file_get_contents($path);
        }
    }
}
