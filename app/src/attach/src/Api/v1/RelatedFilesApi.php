<?php


namespace Akademiano\Attach\Api\v1;

use \Akademiano\Content\Files\Api\v1\FilesApi;
use Akademiano\Attach\Model\LinkedFile;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;

class RelatedFilesApi extends FilesApi
{
    const API_ID = "relatedFilesApi";
    const ENTITY_CLASS = LinkedFile::class;

    const RELATION_API_ID = EntityFileRelationsApi::API_ID;

    /** @var  EntityFileRelationsApi */
    protected $relationsApi;


    /**
     * @return EntityFileRelationsApi
     */
    public function getRelationsApi()
    {
        return $this->relationsApi;
    }

    /**
     * @param EntityFileRelationsApi $relationsApi
     */
    public function setRelationsApi(EntityFileRelationsApi $relationsApi)
    {
        $this->relationsApi = $relationsApi;
    }


    public function deleteEntity(EntityInterface $entity)
    {
        $resource = sprintf('entityapi:delete:%s:%s', static::ENTITY_CLASS, $entity->getId());
        if (!$this->accessCheck($resource, $entity->getOwner())) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        $this->getRelationsApi()->deleteByRelated($entity);

        if ($entity instanceof LinkedFile) {
            $filePath = realpath($this->getRootDir() . DIRECTORY_SEPARATOR . $entity->getPath());
            if ($filePath) {
                unlink($filePath);
            }
        }

        return $this->getOperator()->delete($entity);
    }
}
