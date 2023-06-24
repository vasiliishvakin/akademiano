<?php


namespace Akademiano\Entity;


use Akademiano\Entity\Exception\NotInitializedEntityException;
use Ds\Hashable;

abstract class BaseEntity implements BaseEntityInterface
{
    /** @var ?UuidInterface */
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId(): ?UuidInterface
    {
        if (null !== $this->id && !$this->id instanceof UuidInterface) {
            $this->id = new Uuid($this->id);
        }
        return $this->id;
    }

    public function hasId(): bool
    {
        $id = $this->getId();
        return $id instanceof UuidInterface && $id->getValue();
    }

    /**
     * @deprecated
     */
    public function getUuid(): ?UuidInterface
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
            "id" => $this->getId(),
        ];
    }

    protected function toValuesArray(): array
    {
        return [
            "id" => $this->getId()->getInt(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toValuesArray();
    }

    function hash()
    {
        if (!$this->hasId()) {
            throw new NotInitializedEntityException("Could not get hash for object without id.");
        }

        return hash(UuidableInterface::HASHABLE_ALGO, sprintf("%s%X", get_class($this), $this->getInt()));
    }

    function equals($obj): bool
    {
        if (!is_object($obj)) {
            throw new \InvalidArgumentException("\$obj is not Object");
        }
        if ((!$obj instanceof Hashable) || (get_class($this) !== get_class($obj))) {
            return false;
        }
        return $this->hash() === $obj->hash();
    }
}
