<?php


namespace Akademiano\Api;


use Akademiano\Acl\AccessCheckIncludeInterface;

interface ApiInterface extends AccessCheckIncludeInterface
{
    public function getApiVersion();
}
