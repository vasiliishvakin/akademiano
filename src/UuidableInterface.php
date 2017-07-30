<?php

namespace Akademiano\Entity;


interface UuidableInterface
{
    /**
     * @return UuidInterface
     */
    public function getUuid();
}
