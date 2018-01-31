<?php


namespace Akademiano\Delegating;

use Akademiano\Delegating\Command\CommandInterface;
use Pimple\Container;

interface OperatorInterface
{
    const RESOURCE_ID = 'operator';

    public function execute(CommandInterface $command);

    public function getDependencies():Container;
}
