<?php

namespace Akademiano\UUID;


use Akademiano\Utils\Object\Prototype\StringableInterface;

interface UuidComplexInterface extends StringableInterface
{
    /**
     * @return integer
     */
    public function getValue();

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

    /**
     * @return string
     */
    public function toHex();

}
