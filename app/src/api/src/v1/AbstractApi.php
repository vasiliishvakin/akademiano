<?php


namespace Akademiano\Api\v1;


use Akademiano\Acl\AccessCheckIncludeTrait;
use Akademiano\Api\ApiInterface;

class AbstractApi implements ApiInterface
{
    use AccessCheckIncludeTrait;

    public function getApiVersion()
    {
        return "1.0";
    }

}
