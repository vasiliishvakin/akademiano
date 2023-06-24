<?php


namespace Akademiano\Core;


use Akademiano\Acl\AccessCheckIncludeInterface;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\DI\Container;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\User\CustodianIncludeInterface;
use Pimple\Exception\UnknownIdentifierException;

class ApplicationDiContainer extends Container
{

    protected function prepare($value)
    {
        $value = parent::prepare($value);
        if ($value instanceof AccessCheckIncludeInterface) {
            $value->setAclManager($this["aclManager"]);
        }
        if ($value instanceof CustodianIncludeInterface) {
            $value->setCustodian($this["custodian"]);
        }
        if ($value instanceof ConfigurableInterface) {
            $value->setConfig($this['config']);
        }
        if ($value instanceof EnvironmentIncludeInterface) {
            $value->setEnvironment($this['environment']);
        }
        return $value;
    }

//    public function offsetGet($id)
//    {
//        if (!$this->offsetExists($id)) {
//            throw new UnknownIdentifierException($id);
//        }
//        if (!isset($this->values[$id])) {
//            $this->values[$id] = $this->prepare(parent::offsetGet($id));
//        }
//        return $this->values[$id];
//    }
//
//    public function offsetUnset($id)
//    {
//        unset($this->values[$id]);
//        parent::offsetUnset($id);;
//    }
}
