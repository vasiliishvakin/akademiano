<?php

namespace Akademiano\Acl\Model\Adapter;


interface AdapterInterface
{
    public function isAllow($group, $resource, $user = null, $owner = null);

}
