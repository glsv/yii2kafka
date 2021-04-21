<?php

namespace yii2Kafka\adapters\nmred;

use Kafka\{Consumer, ConsumerConfig, ProducerConfig};
use yii2Kafka\{Config, BaseKafkaAdapter};
use yii2Kafka\exceptions\{DomainException, InvalidConfigException};
use yii2Kafka\interfaces\{KafkaAdapterInterface, KafkaConsumerInterface, KafkaProducerInterface};

class NmredAdapter extends BaseKafkaAdapter implements KafkaAdapterInterface
{
    /**
     * @var NmredProducer
     */
    private static $producer;

    /**
     * @var NmredConsumer
     */
    private static $consumer;

    public function getProducer(): KafkaProducerInterface
    {
        if (self::$producer === null) {
            $this->configureProducer();
            $clientProducer = new \Kafka\Producer();
            if ($this->logger) {
                $clientProducer->setLogger($this->logger);
            }

            self::$producer = new NmredProducer($clientProducer);
            self::$producer->setLogger($this->logger);
            // Disable logging mode because it is already enabled in the Nmred client
            self::$producer->setDebugMode(false);
        }

        return self::$producer;
    }

    public function getConsumer(): KafkaConsumerInterface
    {
        if (self::$consumer === null) {
            ConsumerClientParamsHelper::validateParams($this->params['consumer']);
            self::$consumer = $this->createConsumer(new \yii2Kafka\ConsumerConfig($this->params['consumer']['topics']));
        }

        return self::$consumer;
    }

    public function createConsumer(\yii2Kafka\ConsumerConfig $config): KafkaConsumerInterface
    {
        $cfgGlobal = $this->params['global'] ?? [];
        $cfgConsumer = $this->params['consumer'] ?? [];

        $params = ConsumerClientParamsHelper::prepareParams($cfgGlobal, $cfgConsumer, $config);

        $clientConfig = ConsumerConfig::getInstance();
        $this->initClientConfig($clientConfig, $params);

        $clientConsumer = new Consumer();
        if ($this->logger) {
            $clientConsumer->setLogger($this->logger);
        }

        $consumer = new NmredConsumer($clientConsumer);
        $consumer->setLogger($this->logger);
        // Disable logging mode because it is already enabled in the Nmred client
        $consumer->setDebugMode(false);

        return $consumer;
    }

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    protected function configureProducer(): void
    {
        $extConfig = ProducerConfig::getInstance();
        $extConfig->setMetadataBrokerList($this->config->brokerListForKafka());

        $cfgGlobal = $this->params['global'] ?? [];
        $cfgProducer = $this->params['producer'] ?? [];

        $this->initClientConfig($extConfig, array_merge($cfgGlobal, $cfgProducer));
    }

    protected function initClientConfig($config, array $params): void
    {
        if (!is_a($config, ProducerConfig::class) && !is_a($config, ConsumerConfig::class)) {
            throw new DomainException(
                sprintf('config must be type %s or %s', ProducerConfig::class, ConsumerConfig::class)
            );
        }

        foreach ($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (!method_exists($config, $method)) {
                throw new InvalidConfigException(sprintf('not exist method %s for param %s', $method, $key));
            }
            $config->$method($value);
        }

        $config->setMetadataBrokerList($this->config->brokerListForKafka());
    }
}