<?php

namespace Akademiano\Core;

use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\IO\IO;

interface ConsoleHandlerInterface
{
    public function handle (Args $args, IO $io, Command $command);

}
