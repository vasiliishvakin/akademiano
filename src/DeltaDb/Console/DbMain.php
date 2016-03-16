<?php


namespace DeltaDb\Console;

use DeltaCore\Application;
use DeltaCore\ConfigLoader;
use DeltaDb\Adapter\PgsqlAdapter;
use DeltaUtils\YamlWriter;
use DeltaUtils\YamlReader;
use Webmozart\Console\Api\Args\Args;
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
    public function getDbAdapter()
    {
        if (null === $this->dbAdapter) {
            $config = $this->getApplication()->getConfig(["database", 'default'], []);
            $dbAdapter = new \DeltaDb\Adapter\PgsqlAdapter();
            $dbAdapter->connect("host={$config['host']} user={$config['user']} password={$config['password']} dbname={$config['database']}");
            $this->dbAdapter = $dbAdapter;
        }
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

    public function askOption(Args $args, IO $io, $question, $option, Callable $filter = null)
    {
        $io->writeLine($question);
        $value = trim($io->readLine());
        if (null !== $filter) {
            $value = call_user_func($filter, $value);
        }
        $args->setOption($option, $value);
        return $value;
    }

    public function askArgument(Args $args, IO $io, $question, $argument, Callable $filter = null)
    {
        $io->writeLine($question);
        $value = trim($io->readLine());
        if (null !== $filter) {
            $value = call_user_func($filter, $value);
        }
        $args->setArgument($argument, $value);
        return $value;
    }

    public function writePhinxConfig($host, $user, $password, $database)
    {
        $phinxFile = ROOT_DIR . "/phinx.yml";
        if (!file_exists($phinxFile)) {
            $dir = ROOT_DIR;
            exec("php {$dir}/vendor/robmorgan/phinx/bin/phinx init");
        }

        $data = YamlReader::parseFile($phinxFile);
        $data["environments"]["default_database"] = "production";
        $data["environments"]["production"] = [
            "adapter" => "pgsql",
            "host" => $host,
            "name" => $database,
            "user" => $user,
            "pass" => $password,
            "port" => "5432",
            "charset" => "utf8"
        ];
        $data["paths"]["migrations"]="%%PHINX_CONFIG_DIR%%/migrations";
        $data["paths"]["seeds"]="%%PHINX_CONFIG_DIR%%/seeds";
        unset($data["environments"]["development"]);
        unset($data["environments"]["testing"]);
        return YamlWriter::emitFile($data, $phinxFile, 5);
    }

    public function writeConfig($host, $user, $password, $database)
    {
        $configDir = $this->getApplication()->getConfigLoader()->getConfigDir(ConfigLoader::LEVEL_PROJECT);
        $localConfigFile = "local.config.php";
        $localConfigDistFile = "local.config.dist.php";
        if (!file_exists($configDir . "/" . $localConfigFile)) {
            if (file_exists($configDir . "/" . $localConfigDistFile)) {
                $data = include $configDir . "/" . $localConfigDistFile;
            } else {
                $data = [];
            }
        } else {
            $data = include $configDir . "/" . $localConfigFile;
        }
        if (!is_array($data)) {
            $data = [];
        }
        $data["database"]["default"] ["host"] = $host;
        $data["database"]["default"] ["name"] = $database;
        $data["database"]["default"] ["user"] = $user;
        $data["database"]["default"] ["password"] = $password;

        $content = '<?php' . PHP_EOL . 'return ' . var_export($data, true) . ";\n";
        file_put_contents($configDir ."/" .$localConfigFile, $content, LOCK_EX | FILE_TEXT);
    }

    public function handleCreate(Args $args, IO $io)
    {

        $askMode = $args->isOptionSet("ask");
        if ($askMode) {
            $this->askOption($args, $io, "Do you wont delete db if exist (1|0)", "delete");
            $this->askOption($args, $io, "Do you wont delete disconnect other users (1|0)", "kill");
        }

        $database = $this->getApplication()->getConfig(["database", 'default', "name"]);

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
        if ($args->isOptionSet("owner")) {
            $sql .= " OWNER={$args->getOption("owner")}";
        }
        $sql .= " ENCODING=UTF8";

        $this->debug($args, $io, $sql);
        $result = $this->query($sql, $io);
        if (!$result) {
            return 1;
        }
        return 0;
    }

    public function handleConfig(Args $args, IO $io)
    {
        $askMode = $args->isOptionSet("ask");
        if ($askMode) {
            $this->askOption($args, $io, "Please enter host:", "host");
            $this->askOption($args, $io, "Please enter user:", "user");
            $this->askOption($args, $io, "Please enter password:", "password");
            $this->askArgument($args, $io, "Please enter database name:", "database");
        }

        $host = $args->getOption("host");
        $host = $host ?: "127.0.0.1";
        $user = $args->getOption("user");
        $user = $user ?: "postgres";
        $password = $args->getOption("password");
        $password = $password ?: "123";

        if (!$args->isArgumentSet("database")) {
            $io->errorLine("Error: database name not defined");
            return 1;
        }
        $database = $args->getArgument("database");

        $this->writePhinxConfig($host, $user, $password, $database);
        $this->writeConfig($host, $user, $password, $database);
        return 0;
    }
}
