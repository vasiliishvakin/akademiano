<?php

namespace Akademiano\Content\Comments\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\Object\Collection;

class Comment extends ContentEntity
{
    const ENTITY_CLASS = Entity::class;
    const ENTITY_FILES_CLASS = CommentFile::class;

    /** @var EntityInterface */
    protected $entity;
    /** @var Collection|CommentFile[]|array */
    protected $files;

    use OwneredTrait;


    public function getEntity():EntityInterface
    {
        if (null !== $this->entity && !$this->entity instanceof EntityInterface) {
            $entity = $this->delegate((new GetCommand(static::ENTITY_CLASS))->setId($this->entity));
            if (!$entity) {
                throw new NotFoundException(sprintf('Entity of class "%s" with id "%s" not found'), static::ENTITY_CLASS, $this->entity);
            }
            $this->entity = $entity;
        }
        return $this->entity;
    }

    /**
     * @param EntityInterface $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Collection|CommentFile[]
     */
    public function getFiles():Collection
    {
        if (!$this->files instanceof Collection) {
            if (is_array($this->files)) {
                $criteria = ["id" => $this->files];
            } else {
                $criteria = ["entity" => $this];
            }
            $this->files = $this->delegate((new FindCommand(static::ENTITY_FILES_CLASS))->setCriteria($criteria));
        }
        return $this->files;
    }
}
