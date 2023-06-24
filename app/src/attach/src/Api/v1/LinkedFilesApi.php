<?php


namespace Akademiano\Attach\Api\v1;

use Akademiano\Attach\Command\ParseRequestFilesCommand;
use Akademiano\Attach\Model\LinkedFile;
use Akademiano\Attach\Model\LinkedFilesWorker;
use Akademiano\Attach\Model\RequestFiles;
use Akademiano\Attach\Module;
use \Akademiano\Content\Files\Api\v1\FilesApi;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\HttpWarp\Request;
use Akademiano\Utils\ArrayTools;

class LinkedFilesApi extends FilesApi
{
    const API_ID = "linkedFilesApi";
    const ENTITY_CLASS = LinkedFile::class;
    const MODULE_ID = Module::MODULE_ID;

    public function parseHttpRequestFiles(Request $request): RequestFiles
    {
        $files = $this->delegate((new ParseRequestFilesCommand($request)));
        return $files;
    }

    public function processHttpRequestFiles(Request $request, EntityInterface $entity)
    {
        $files = $this->parseHttpRequestFiles($request);

        $deleted = $files->getDeleted();
        foreach ($deleted as $id) {
            /** @var LinkedFile $file */
            $file = $this->get($id)->getOrThrow(new NotFoundException(sprintf('Not found file with id "%s"', dechex($id))));
            if ($file->getEntity()->getInt() !== $entity->getInt()) {
                throw new \RuntimeException(sprintf(
                    'Not allow delete linked files to another ("%s"), not the current entity ("%s")',
                    $file->getEntity()->getId()->getHex(),
                    $entity->getId()->getHex()
                ));
            }
            $this->deleteEntity($file);
        }

        $uploaded = $files->getUploaded();
        foreach ($uploaded as $fileInfo) {
            $attributes = $fileInfo->toArray();
            $attributes = ArrayTools::filterNulls($attributes);
            $attributes[LinkedFilesWorker::LINKED_ENTITY_FIELD] = $entity;
            unset($attributes['uploadedFile']);
            $file = $this->saveUploaded($fileInfo->getUploadedFile(), $attributes);
        }

        $updated = $files->getUpdated();
        foreach ($updated as $id=>$fileInfo) {
            $file = $this->get($id)->getOrThrow(new NotFoundException(sprintf('Not found file with id "%s"', dechex($id))));
            if ($file->getEntity()->getInt() !== $entity->getInt()) {
                throw new \RuntimeException(sprintf(
                    'Not allow update linked files to another ("%s"), not the current entity ("%s")',
                    $file->getEntity()->getId()->getHex(),
                    $entity->getId()->getHex()
                ));
            }
            if (isset($fileInfo[LinkedFilesWorker::LINKED_ENTITY_FIELD])) {
                  unset($fileInfo[LinkedFilesWorker::LINKED_ENTITY_FIELD]);
            }
            $this->delegate((new LoadCommand($file))->setData($fileInfo));
            $this->saveEntity($file);
        }
    }
}
