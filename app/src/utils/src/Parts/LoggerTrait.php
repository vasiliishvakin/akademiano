<?php


namespace Akademiano\Utils\Parts;


use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    /** @var  LoggerInterface */
    protected $logger;

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log($level, $message, array $context = []):void
    {
        $logger = $this->getLogger();
        if ($logger instanceof LoggerInterface) {
            $this->getLogger()->log($level, $message, $context);
        }
    }
}
