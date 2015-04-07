<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Adapter;


class DenyAdapter implements AdapterInterface
{
    public function isAllow($group, $resource, $user = null, $owner = null)
    {
        return false;
    }

} 