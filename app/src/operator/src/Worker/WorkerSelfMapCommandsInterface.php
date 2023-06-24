<?php


namespace Akademiano\Operator\Worker;


interface WorkerSelfMapCommandsInterface
{
    const SELF_MAP_COMMAND_NAME = 'getSelfCommandsMapping';

    const DEFAULT_ORDER = 0;

    public static function getSelfCommandsMapping(array $overwrite = []): array;
}
