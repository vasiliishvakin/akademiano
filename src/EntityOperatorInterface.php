<?php


namespace Akademiano\EntityOperator;

use Akademiano\Operator\OperatorInterface;

interface EntityOperatorInterface extends OperatorInterface, FinderInterface, KeeperInterface, CreatorInterface, LoaderInterface, GenerateIdInterface
{

}
