<?php

namespace yii2Kafka;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use yii2Kafka\exceptions\RuntimeException;
use yii2Kafka\interfaces\{KafkaAdapterInterface, KafkaConsumerInterface, KafkaLoggerInterface, KafkaProducerInterface};
use yii\base\Component;
use yii\base\InvalidConfigException;

class YiiKafkaComponent extends Component
{
    public $adapter;
    public $brokers = [];
    public $version = '1.0.0';
    public $params = [];
    public $loggerFactory;
    public $defaultLogPath = '@runtime/logs/kafka.log';
    public $defaultLogLevel = Logger::DEBUG;
    public $debugMode = false;

    /**
     * @var KafkaAdapterInterface
     */
    protected $adapterClient;

    public function init(): void
    {
        $this->validateParams();
        $this->adapterClient = \Yii::createObject($this->adapter);

        $config = new Config();
        $config->setBrokers($this->brokers);
        $this->adapterClient->setConfig($config);

        $logger = $this->getLogger();
        $this->adapterClient->setLogger($logger);
        $this->adapterClient->setDebugMode($this->debugMode);

        parent::init();
    }

    protected function getDefaultLogger(): LoggerInterface
    {
        $logger = new Logger('kafka');
        $logger->pushHandler(new StreamHandler(\Yii::getAlias($this->defaultLogPath), $this->defaultLogLevel));
        return $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        if (empty($this->loggerFactory)) {
            return $this->getDefaultLogger();
        }

        if (is_callable($this->loggerFactory)) {
            $loggerFactory = call_user_func($this->loggerFactory);
            return $loggerFactory->getLogger();
        }

        if (is_object($this->loggerFactory)) {
            if (!is_a($this->loggerFactory, KafkaLoggerInterface::class)) {
                throw new RuntimeException('loggerFactory must be a type ' . KafkaLoggerInterface::class);
            }

            return $this->loggerFactory->getLogger();
        }

        if (is_string($this->loggerFactory)) {
            $loggerFactory = \Yii::createObject($this->loggerFactory);
            if (!($loggerFactory instanceof KafkaLoggerInterface)) {
                throw new RuntimeException('loggerClass must be a type' . KafkaLoggerInterface::class);
            }

            return $loggerFactory->getLogger();
        }

        throw new RuntimeException('logger creation error');
    }

    protected function validateParams(): void
    {
        if (is_null($this->adapter)) {
            throw new InvalidConfigException('adapter is empty');
        }

        if (!isset($this->adapter['class'])) {
            throw new InvalidConfigException('not found adapter[class]');
        }

        if (empty($this->brokers)) {
            throw new InvalidConfigException('brokers is empty');
        }

        if (!is_array($this->brokers)) {
            throw new InvalidConfigException('brokers must be an array');
        }

        if (!is_array($this->params) && !is_null($this->params)) {
            throw new InvalidConfigException('params value must be an array');
        }
    }

    public function getProducer(): KafkaProducerInterface
    {
        return $this->adapterClient->getProducer();
    }

    public function getConsumer(): KafkaConsumerInterface
    {
        return $this->adapterClient->getConsumer();
    }

    public function getAdapterClient(): KafkaAdapterInterface
    {
        return $this->adapterClient;
    }
}