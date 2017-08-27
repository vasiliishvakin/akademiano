<?php


namespace Akademiano\Core;


use Akademiano\Acl\AccessCheckIncludeInterface;
use Akademiano\DI\Container;
use Akademiano\User\CustodianIncludeInterface;
use Pimple\Exception\UnknownIdentifierException;

class ApplicationDiContainer extends Container
{
    protected $values = [];

    protected function prepare($value)
    {
        if ($value instanceof AccessCheckIncludeInterface) {
            $value->setAclManager($this["aclManager"]);
        }
        if ($value instanceof CustodianIncludeInterface) {
            $value->setCustodian($this["custodian"]);
        }
        if ($value instanceof \Akademiano\Config\ConfigurableInterface) {
            $value->setConfig($this["config"]);
        }
        return $value;
    }

    public function offsetGet($id)
    {
        if (!$this->offsetExists($id)) {
            throw new UnknownIdentifierException($id);
        }
        if (!isset($this->values[$id])) {
            $this->values[$id] = $this->prepare(parent::offsetGet($id));
        }
        return $this->values[$id];
    }

    public function offsetUnset($id)
    {
        unset($this->values[$id]);
        parent::offsetUnset($id);;
    }
}
