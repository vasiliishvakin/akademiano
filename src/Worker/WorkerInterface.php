<?php


namespace Akademiano\Operator\Worker;


use Akademiano\Operator\Command\CommandInterface;

interface WorkerInterface
{
    public function execute(CommandInterface $command);

    public static function getMetadata(array $metadata = null, $replace = true);

    public static function getMapping($mappingClass = null, $replace = true);
}
