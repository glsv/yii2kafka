<?php

namespace yii2Kafka\traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debugMode;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setDebugMode(bool $mode): void
    {
        $this->debugMode = $mode;
    }

    public function debug($message, array $context = []): void
    {
        if ($this->debugMode) {
            $this->log(LogLevel::DEBUG, $message, $context);
        }
    }

    public function log($level, $message, array $context = []): void
    {
        if ($this->logger === null) {
            return;
        }
        $this->logger->log($level, $message, $context);
    }
}