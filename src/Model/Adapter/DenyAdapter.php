<?php

namespace Akademiano\Acl\Model\Adapter;


class DenyAdapter implements AdapterInterface
{
    public function isAllow($group, $resource, $user = null, $owner = null)
    {
        return false;
    }

}
