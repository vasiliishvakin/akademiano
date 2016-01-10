<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 10.01.16
 * Time: 18:26
 */

namespace UUID\Model\Parts;


trait UuidFactoryTrait
{
    /** @var  \UUID\Model\UuidFactory */
    protected $uuidFactory;

    /**
     * @return \UUID\Model\UuidFactory
     */
    public function getUuidFactory()
    {
        return $this->uuidFactory;
    }

    /**
     * @param \UUID\Model\UuidFactory $uuidFactory
     */
    public function setUuidFactory($uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }
}