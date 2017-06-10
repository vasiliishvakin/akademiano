<?php


namespace Akademiano\Api\v1;


use Akademiano\Api\ApiInterface;

class AbstractApi implements ApiInterface
{
    public function getApiVersion()
    {
        return "1.0";
    }

}
