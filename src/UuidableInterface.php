<?php

namespace Akademiano\Entity;

use Ds\Hashable;


interface UuidableInterface extends Hashable
{

    public const HASHABLE_ALGO = "sha256";

        /**
     * @return UuidInterface
     */
    public function getUuid();
}
