<?php

namespace Akademiano\Acl\Model\Adapter;


use Akademiano\Config\ConfigurableInterface;
use Akademiano\Config\ConfigurableTrait;

abstract class AbstractAdapter implements AdapterInterface, ConfigurableInterface
{
    use ConfigurableTrait;

    abstract public function isAllow($group, $resource, $user = null, $owner = null);

}
