<?php
/**
 * Адаптер для клиента longyan/phpkafka
 * https://github.com/longyan/phpkafka
 */

namespace yii2Kafka\adapters\longlang;

use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumerConfig as ClientConfig;
use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;
use yii2Kafka\BaseKafkaAdapter;
use yii2Kafka\Config;
use yii2Kafka\exceptions\InvalidConfigException;
use yii2Kafka\interfaces\{KafkaAdapterInterface, KafkaConsumerInterface, KafkaProducerInterface};
use yii2Kafka\ConsumerConfig;

class LonglangAdapter extends BaseKafkaAdapter implements KafkaAdapterInterface
{
    protected static $producer;
    protected static $consumer;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function getProducer(): KafkaProducerInterface
    {
        if (self::$producer === null) {
            $extConfig = new ProducerConfig($this->params['producer'] ?? []);
            $extConfig->setBootstrapServer($this->config->brokerListForKafka());

            self::$producer = new LonglangProducer(new Producer($extConfig));
            self::$producer->setLogger($this->logger);
            self::$producer->setDebugMode($this->debugMode);
        }

        return self::$producer;
    }

    public function getConsumer(): KafkaConsumerInterface
    {
        if (!is_array($this->params['consumer'])) {
            throw new InvalidConfigException('consumer must set in params');
        }

        ConsumerClientParamsHelper::validateParams($this->params['consumer']);

        if (self::$consumer === null) {
            $topic = implode(',', $this->params['consumer']['topic']);
            self::$consumer = $this->createConsumer(new ConsumerConfig($topic));
        }

        return self::$consumer;
    }

    public function createConsumer(ConsumerConfig $config): LonglangConsumer
    {
        $params = ConsumerClientParamsHelper::prepareParams($this->params['consumer'], $config);
        $clientConfig = new ClientConfig($params);
        $clientConfig->setBootstrapServer($this->config->brokerListForKafka());

        $consumer = new LonglangConsumer(new Consumer($clientConfig));
        $consumer->setLogger($this->logger);
        $consumer->setDebugMode();
        return $consumer;
    }
}