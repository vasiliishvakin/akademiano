<?php


namespace Acl\Model;


interface AccessControlSimple
{
    /**
     * @return bool
     */
    public function isAllow();
}
