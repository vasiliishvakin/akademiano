<?php

namespace Akademiano\UUID\Parts;


trait UuidFactoryTrait
{
    /** @var  \Akademiano\UUID\UuidFactory */
    protected $uuidFactory;

    /**
     * @return \Akademiano\UUID\UuidFactory
     */
    public function getUuidFactory()
    {
        return $this->uuidFactory;
    }

    /**
     * @param \Akademiano\UUID\UuidFactory $uuidFactory
     */
    public function setUuidFactory($uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }
}
