<?php

namespace yii2Kafka\adapters\nmred;

use Kafka\Consumer;
use yii2Kafka\BaseKafkaConsumer;
use yii2Kafka\interfaces\KafkaConsumerInterface;
use yii2Kafka\Message;

class NmredConsumer extends BaseKafkaConsumer implements KafkaConsumerInterface
{
    /**
     * @var Consumer
     */
    protected $consumer;

    public function __construct(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    protected function consumeInternal(\Closure $handler): void
    {
        $this->consumer->start(function ($topicName, $part, $message) use ($handler) {
            $mess = new Message($topicName, $message['message']['value']);
            $mess->setKey($message['key'] ?? '');
            $handler($mess);
        });
    }

    public function getClient(): Consumer
    {
        return $this->consumer;
    }
}