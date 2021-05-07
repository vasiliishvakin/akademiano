<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Config\Config;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\Config\ConfigurableTrait;
use Akademiano\Config\Permanent\PermanentConfig;
use Akademiano\Config\Permanent\PermanentStorageFile;
use Akademiano\Config\Permanent\PermanentStorageInterface;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\EntityOperator\Command\GetTableIdCommand;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;

class TablesIdsWorker implements WorkerInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface, ConfigurableInterface
{
    public const WORKER_ID = 'tablesIdsWorker';
    public const CONFIG_TABLE_ID_KEY = 'tableId';

    use WorkerSelfInstanceTrait;
    use WorkerMappingTrait;
    use ConfigurableTrait;

    /** @var bool */
    private $changed = false;

    public function __construct()
    {
        register_shutdown_function([$this, 'save']);
    }


    public static function getSupportedCommands(): array
    {
        return [
            GetTableIdCommand::class,
        ];
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        return null;
    }


    public function execute(CommandInterface $command)
    {
        switch (true) {
            case $command instanceof GetTableIdCommand:
                return $this->getTableId($command->getWorkerId());
            default:
                throw new NotSupportedCommandException($command);
        }
    }

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->changed;
    }

    /**
     * @param bool $changed
     */
    public function setChanged(): void
    {
        $this->changed = true;
    }

    protected function getConfigPath(): array
    {
        return PostgresEntityWorker::CONFIG_PATH;
    }

    public function getWorkersConfig($path = null): Config
    {
        $fullPath = $this->getConfigPath();
        if (null !== $path) {
            array_push($fullPath, (array)$path);
        }
        return $this->getConfig($fullPath, []);
    }

    protected function getTableIdConfigSubPath(string $workerId): array
    {
        return [$workerId, self::CONFIG_TABLE_ID_KEY];
    }

    public function setTableId(string $workerId, int $value)
    {
        $path = $this->getConfigPath();
        array_push($path, ...$this->getTableIdConfigSubPath($workerId));
        $this->getConfig()->set($value, $path);
        $this->setChanged();
    }

    public function generateId(string $workerId): int
    {
        $data = $this->getWorkersConfig()->toCollection()->lists(self::CONFIG_TABLE_ID_KEY)->toArray();
        if (empty($data)) {
            $tableId = 1;
        } else {
            $max = max($data);
            $tableId = $max + 1;
        }
        $this->setTableId($workerId, $tableId);
        return $tableId;
    }

    public function getTableId(string $workerId)
    {
        return $this->getWorkersConfig()->getOrCall(
            $this->getTableIdConfigSubPath($workerId),
            [$this, 'generateId'],
            [$workerId]
        );
    }

    public function getPermanentConfigStorage(): PermanentStorageInterface
    {
        $configStorage = $this->getWorkersConfig()->getOrThrow([self::WORKER_ID, 'configStorage']);
        $storageDriver = array_key_first($configStorage->toArray());
        if ($storageDriver instanceof PermanentConfig) {
            throw new \Exception(sprintf('%s not instance of %s', $storageDriver, PermanentConfig::class));
        }
        $params = $configStorage->get($storageDriver, [])->toArray();
        $storage = new $storageDriver(...array_values($params));
        return $storage;
    }

    //TODO Fix full workers config info save
    public function save(): void
    {
        if ($this->isChanged()) {
            $config = $this->getWorkersConfig();
//            $config->unset([self::WORKER_ID, 'configStorage']);
            $config->toPermanent($this->getPermanentConfigStorage(), $this->getConfigPath());
        }
    }
}
