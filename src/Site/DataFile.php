<?php


namespace Akademiano\Sites\Site;


class DataFile extends File
{
    public function __construct()
    {
        throw new \LogicException(sprintf('Class "%s" not usable', __CLASS__));
    }
}
