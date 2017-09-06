<?php

namespace Akademiano\Content\Comments\Model;


use Akademiano\Entity\ContentEntity;
use Akademiano\Entity\Entity;
use Akademiano\Entity\NamedEntityInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\UserEO\Model\Utils\OwneredTrait;
use Akademiano\Utils\Object\Collection;

class Comment extends ContentEntity implements NamedEntityInterface, DelegatingInterface
{
    const ENTITY_FILES_CLASS = CommentFile::class;

    use DelegatingTrait;
    use OwneredTrait;

    /** @var  Entity */
    protected $entity;

    protected $files;


    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Collection|CommentFile[]
     */
    public function getFiles()
    {
        if (!$this->files instanceof Collection) {
            if (is_array($this->files)) {
                $criteria = ["id" => $this->files];
            } else {
                $criteria = ["entity" => $this];
            }
            $this->files = $this->getOperator()->find(static::ENTITY_FILES_CLASS, $criteria);
        }
        return $this->files;
    }
}
