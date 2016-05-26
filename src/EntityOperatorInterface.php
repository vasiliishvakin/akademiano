<?php


namespace EntityOperator;

use EntityOperator\Operator\FinderInterface;
use EntityOperator\Operator\KeeperInterface;
use EntityOperator\Operator\CreatorInterface;
use EntityOperator\Operator\LoaderInterface;

interface EntityOperatorInterface extends FinderInterface, KeeperInterface, CreatorInterface, LoaderInterface
{

}
