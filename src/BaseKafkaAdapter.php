<?php

namespace yii2Kafka;

use Psr\Log\LoggerInterface;

abstract class BaseKafkaAdapter
{
    public $params;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}