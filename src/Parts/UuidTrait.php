<?php

namespace Akademiano\UUID\Parts;

use Akademiano\UUID\UuidComplexInterface;

trait UuidTrait
{
    /** @var  \Akademiano\UUID\UuidComplexShort */
    protected $uuid;

    /**
     * @return \Akademiano\UUID\UuidFactory
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
