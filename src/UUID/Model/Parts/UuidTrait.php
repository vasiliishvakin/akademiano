<?php

namespace UUID\Model\Parts;

use UUID\Model\UuidComplexInterface;

trait UuidTrait
{
    /** @var  \UUID\Model\UuidComplexShort */
    protected $uuid;

    /**
     * @return \UUID\Model\UuidFactory
     */
    abstract public function getUuidFactory();


    /**
     * @return UuidComplexInterface
     */
    public function getUuid()
    {
        if (null !== $this->uuid && !$this->uuid instanceof UuidComplexInterface) {
            $this->uuid = $this->getUuidFactory()->create($this->uuid);
        }
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

}
