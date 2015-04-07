<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Adapter;


use DeltaCore\ConfigurableInterface;
use DeltaCore\Parts\Configurable;

abstract class AbstractAdapter implements AdapterInterface, ConfigurableInterface
{
    use Configurable;

    abstract public function isAllow($group, $resource, $user = null, $owner = null);

} 