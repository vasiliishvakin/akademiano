<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Adapter;


interface AdapterInterface
{
    public function isAllow($group, $resource, $user = null, $owner = null);

} 