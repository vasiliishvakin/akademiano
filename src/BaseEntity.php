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

    public function getInt()
    {
        return $this->getId()->getInt();
    }

    public function __toString()
    {
        return $this->getId()->__toString();
    }

    public function toArray()
    {
        return [
            "id"=> $this->getId(),
        ];
    }

}
