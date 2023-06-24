<?php


namespace Akademiano\Utils\Object;


use Carbon\CarbonInterval;

class AkademianoInterval extends CarbonInterval implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return $this->spec();
    }
}
