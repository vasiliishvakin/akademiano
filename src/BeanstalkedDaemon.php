<?php
declare(strict_types=1);

namespace Akademiano\Daemon;

use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

abstract class BeanstalkedDaemon
{
    const CONFIG_BEANSTALK_NAME = 'beanstalk';
    const BEANSTALK_TUBE = 'akademiano_queue';

    /** @var  Pheanstalk */
    protected $beanstalk;

    protected $isRun = true;

    /** @var  array */
    protected $config = [];

    protected $beanstalkDsn;

    protected $tickTimeout = 50;

    protected $concurrency = 2;


    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getBeanstalkDsn()
    {
        if (null === $this->beanstalkDsn) {
            $config = $this->getConfig();
            $default = [
                'host' => '127.0.0.1',
                'port' => PheanstalkInterface::DEFAULT_PORT,
                'connectTimeout' => null,
                'connectPersistent' => false,
            ];
            if (!isset($config[self::CONFIG_BEANSTALK_NAME])) {
                $this->beanstalkDsn = $default;
            } else {
                $this->beanstalkDsn = array_merge_recursive($config[self::CONFIG_BEANSTALK_NAME], $default);
            }
        }
        return $this->beanstalkDsn;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function getTickTimeout(): int
    {
        return $this->tickTimeout;
    }

    /**
     * @param int $tickTimeout
     */
    public function setTickTimeout(int $tickTimeout)
    {
        $this->tickTimeout = $tickTimeout;
    }

    /**
     * @return Pheanstalk
     */
    public function getBeanstalk(): Pheanstalk
    {
        if (null === $this->beanstalk) {
            $dsn = $this->getBeanstalkDsn();
            $this->beanstalk = new Pheanstalk($dsn['host'], $dsn['port'], $dsn['connectTimeout'], $dsn['connectPersistent']);
            $this->beanstalk->watch(self::BEANSTALK_TUBE)
                ->ignore('default');
        }
        return $this->beanstalk;
    }

    /**
     * @param Pheanstalk $beanstalk
     */
    public function setBeanstalk(Pheanstalk $beanstalk)
    {
        $this->beanstalk = $beanstalk;
    }


    /**
     * @return bool
     */
    public function isRun(): bool
    {
        return $this->isRun;
    }

    /**
     * @param bool $isRun
     */
    public function setIsRun(bool $isRun)
    {
        $this->isRun = $isRun;
    }

    public function getConcurrency(): int
    {
        return $this->concurrency;
    }

    public function getMessageIdFromJob(Job $job):?string
    {
        $data = $job->getData();
        try {
            $data = json_decode($data, true);
        } catch (\Throwable $e) {
            return null;
        }
        return isset($data['id']['value']) ? (string)$data['id']['value'] : null;
    }

    public function sigHandler(int $sigNum, array $sigInfo): void
    {
        switch ($sigNum) {
            case SIGTERM:
                echo "Получен сигнал SIGTERM...\n";
                $this->setIsRun(false);
                break;
            case SIGHUP:
                echo "Получен сигнал SIGHUP...\n";
                break;
            case SIGUSR1:
                echo "Получен сигнал SIGUSR1...\n";
                break;
            default:
                echo "Получен сигнал $sigNum...\n";
        }
    }

    abstract public function processNextJobs(): void;

    public function run()
    {
        pcntl_signal(SIGTERM, [$this, "sigHandler"]);
        pcntl_signal(SIGHUP, [$this, "sigHandler"]);
        pcntl_signal(SIGUSR1, [$this, "sigHandler"]);

        echo sprintf('Started, pid: %s' . PHP_EOL, posix_getpid());

        while ($this->isRun()) {
            $this->processNextJobs();

            pcntl_signal_dispatch();

            if ($this->isRun()) {
                sleep($this->getTickTimeout());
            }
        }
        echo 'Terminate' . PHP_EOL;
    }
}