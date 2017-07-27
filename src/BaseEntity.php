<?php


namespace Akademiano\Entity;


abstract class BaseEntity implements BaseEntityInterface
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        if (null !== $this->id && !$this->id instanceof UuidInterface) {
            $this->id = new Uuid($this->id);
        }
        return $this->id;
    }

    public function getUuid()
    {
        return $this->getId();
    }

    public function toInt()
    {
        return $this->getId()->toInt();
    }

    public function __toString()
    {
        return $this->getId()->__toString();
    }


}
