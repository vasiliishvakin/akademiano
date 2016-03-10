<?php


namespace DeltaDb\Console;

use DeltaCore\Application;
use DeltaDb\Adapter\PgsqlAdapter;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\IO\IO;

class DbMain
{

    /** @var  Application */
    protected $application;

    /** @var  PgsqlAdapter */
    protected $dbAdapter;

    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }


    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return PgsqlAdapter
     */
    public function initDbAdapter($host, $user, $password)
    {
        $dbAdapter = new \DeltaDb\Adapter\PgsqlAdapter();
        $dbAdapter->connect("host={$host} user={$user} password={$password}");
        $this->setDbAdapter($dbAdapter);
    }

    /**
     * @return PgsqlAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param PgsqlAdapter $dbAdapter
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function query($sql, IO $io)
    {
        $db = $this->getDbAdapter();
        $result = $db->query($sql);
        if (!$result) {
            $io->errorLine($db->getError());
            return false;
        }
        return true;
    }

    public function debug(Args $args, IO $io, $message)
    {
        $verbose = $args->isOptionSet("verbose");
        if ($verbose) {
            $io->writeLine($message);
        }
    }

    public function handleCreate(Args $args, IO $io)
    {
        $this->initDbAdapter($args->getOption("host"), $args->getOption("user"), $args->getOption("password"));

        $database = $args->getArgument("database");

        if ($args->isOptionSet("delete")) {
            if ($args->isOptionSet("kill")) {
                $sql = "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '{$database}' AND pid <> pg_backend_pid()";
                $this->debug($args, $io, $sql);
                $result = $this->query($sql, $io);
                if (!$result) {
                    return 1;
                }
            }
            $sql = "DROP DATABASE IF EXISTS {$database}";
            $this->debug($args, $io, $sql);
            $result = $this->query($sql, $io);
            if (!$result) {
                return 1;
            }
        }

        $sql = "CREATE DATABASE {$database} WITH";
        if ($args->getOption("owner")) {
            $sql .= " OWNER={$args->getOption("owner")}";
        }
        $sql .= " ENCODING=UTF8";

        $this->debug($args, $io, $sql);
        $result = $this->query($sql, $io);
        if (!$result) {
            return 1;
        }
    }


}