<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 10.01.16
 * Time: 16:23
 */

namespace UUID\Model;


use DeltaUtils\Object\Prototype\StringableInterface;

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
