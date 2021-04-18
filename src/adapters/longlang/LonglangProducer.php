<?php

namespace yii2Kafka\adapters\longlang;

use longlang\phpkafka\Producer\ProduceMessage;
use longlang\phpkafka\Producer\Producer;
use yii2Kafka\interfaces\KafkaProducerInterface;
use yii2Kafka\Message;
use yii2Kafka\BaseKafkaProducer;

class LonglangProducer extends BaseKafkaProducer implements KafkaProducerInterface
{
    /**
     * @var Producer
     */
    protected $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function sendInternal(string $topicName, $value, $key = ''): void
    {
        $this->producer->send($topicName, $value, $key);
    }

    /**
     * @param Message[] $messages
     */
    public function sendBatchInternal(array $messages): void
    {
        $packet = [];
        foreach ($messages as $message) {
            $packet[] = new ProduceMessage($message->topicName(), $message->value(), $message->key());
        }

        $this->producer->sendBatch($packet);
    }

    public function getClient(): Producer
    {
        return $this->producer;
    }
}