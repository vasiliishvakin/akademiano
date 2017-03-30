<?php

namespace Akademiano\Acl\Model\Adapter;


class AllowAdapter implements AdapterInterface
{
    public function isAllow($group, $resource, $user = null, $owner = null)
    {
        return true;
    }

}
