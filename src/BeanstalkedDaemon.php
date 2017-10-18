<?php
declare(strict_types=1);

namespace Akademiano\Daemon;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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

    /** @var  Logger */
    protected $logger;


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
            $this->beanstalk->watch(static::BEANSTALK_TUBE)
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
                $this->log(Logger::INFO, 'Get SIGTERM', ['siginfo'=>$sigInfo]);
                $this->setIsRun(false);
                break;
            case SIGHUP:
                $this->log(Logger::INFO, 'Get SIGHUP', ['siginfo'=>$sigInfo]);
                break;
            case SIGUSR1:
                $this->log(Logger::INFO, 'Get SIGUSR1', ['siginfo'=>$sigInfo]);
                break;
            default:
                $this->log(Logger::INFO, sprintf('Get %s', $sigNum), ['siginfo'=>$sigInfo]);
        }
    }

    public function getLogDir()
    {
        $logDir = ROOT_DIR . '/data/log';
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0770, true)) {
                throw new \RuntimeException(sprintf('Could not create log directory "%s"', $logDir));
            }
        }
        return $logDir;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        if (null === $this->logger) {
            $this->logger = new Logger('herald');
            $this->logger->pushHandler(new StreamHandler($this->getLogDir(). '/daemon.log', Logger::INFO));
            $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::ERROR));
        }
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function log($level, $message, array $context = []):bool
    {
        return $this->getLogger()->log($level, $message, $context);
    }


    abstract public function processNextJobs(): void;

    public function run()
    {
        pcntl_signal(SIGTERM, [$this, "sigHandler"]);
        pcntl_signal(SIGHUP, [$this, "sigHandler"]);
        pcntl_signal(SIGUSR1, [$this, "sigHandler"]);

        $this->log(Logger::INFO, sprintf('Started'), ['pid'=>posix_getpid()]);

        while ($this->isRun()) {
            $this->processNextJobs();

            pcntl_signal_dispatch();

            if ($this->isRun()) {
                sleep($this->getTickTimeout());
            }
        }
        $this->log(Logger::INFO, 'Shutdown');
    }
}
