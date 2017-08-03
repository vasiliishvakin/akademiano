<?php

namespace Akademiano\UUID;


use Akademiano\Entity\UuidInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidComplexInterface extends UuidInterface
{
    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * @return integer
     */
    public function getShard();

    /**
     * @return integer
     */
    public function getId();

}
