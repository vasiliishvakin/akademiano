<?php


namespace App\Console;

use Symfony\Component\Process\Process;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;


class Git
{
    public function debug(Args $args, IO $io, $message)
    {
        $verbose = $args->isOptionSet("verbose");
        if ($verbose) {
            $io->writeLine($message);
        }
    }

    public function runProcess(Args $args, IO $io, $command, $echoError = true)
    {
        $process = new Process($command, ROOT_DIR);
        $this->debug($args, $io, $command);
        try {
            $process->mustRun();
        } catch (\Exception $e) {
            if ($echoError) {
                $io->errorLine($e->getMessage());
            }
            return false;
        }
        return $process->getOutput();
    }

    public function getBranch(Args $args, IO $io)
    {
        $cmd = 'git branch | grep "*" | awk \'{print($2)}\'';
        $result = $this->runProcess($args, $io, $cmd);
        if (!$result) {
            return false;
        }
        if ($result === "(detached") {
            $result = "!detached!";
        }
        return trim($result);
    }

    public function handleBranch(Args $args, IO $io)
    {
        $branch = $this->getBranch($args, $io);
        if (!$branch) {
            return 1;
        }
        $io->writeLine($branch);
    }

    public function getVersion(Args $args, IO $io)
    {
        $cmd = 'git describe --exact-match --abbrev=0 --tags';
        $result = $this->runProcess($args, $io, $cmd, false);
        if ($result) {
            return $result;
        }

        $branch = $this->getBranch($args, $io);
        if (!$branch) {
            return 1;
        }

        $version = false;
        switch ($branch) {
            case "!detached": {
                $version = "detached";
                break;
            }
            case "release":
            case "master": {
                $cmd = 'git describe --abbrev=0 --tags';
                $subVersion = $this->runProcess($args, $io, $cmd);
                if (!$subVersion) {
                    $subVersion = "";
                }
                $version = $subVersion . "-dev-" . $branch;
                break;
            }
            default: {
                $cmd = 'git rev-parse --short `git log -n 1 | head -n 1 | sed -e \'s/^commit //\'`';
                $subVersion = $this->runProcess($args, $io, $cmd);
                if (!$subVersion) {
                    return 1;
                }
                $version = $branch . "-dev-" . $subVersion;
            }
        }
        return $version;
    }

    public function handleVersion(Args $args, IO $io)
    {
        $version = $this->getVersion($args, $io);
        if (!$version) {
            return 1;
        }
        $io->writeLine($version);
    }
}
